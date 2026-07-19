"use client";

import * as React from "react";
import Link from "next/link";
import Image from "next/image";
import { Search, ShoppingBag, User, Heart, Menu } from "lucide-react";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Sheet, SheetContent, SheetTrigger } from "@/components/ui/sheet";
import { useAuthStore } from "@/store/slices/authSlice";
import { getRelativePath } from "@/lib/utils";
import { useCartStore } from "@/store/useCartStore";
interface MegaMenuItem {
  title: string;
  url: string;
}

interface MegaMenuColumn {
  title: string;
  url: string;
  items: MegaMenuItem[];
}
// Descriptive taxonomy categories for standard pages, and price tags only for GenZ
const megaMenuData: Record<string, { title: string; items: string[] }[]> = {
  men: [
    {
      title: "Topwear",
      items: ["T-Shirts", "Casual Shirts", "Jeans"],
    },
    {
      title: "Footwear",
      items: ["Casual Shoes", "Sneakers"],
    },
  ],

  women: [
    {
      title: "Western Wear",
      items: ["Dresses", "Tops", "Jeans"],
    },
    {
      title: "Footwear",
      items: ["Heels", "Flats"],
    },
  ],

  kids: [
    {
      title: "Boys Clothing",
      items: ["T-Shirts", "Jeans"],
    },
  ],

  home: [
    {
      title: "Home Decor",
      items: ["Wall Art", "Mirrors"],
    },
  ],

  beauty: [
    {
      title: "Makeup",
      items: ["Face Makeup", "Lipsticks"],
    },
  ],

  genz: [
    {
      title: "Trending",
      items: ["Dresses", "T-Shirts", "Sneakers"],
    },
  ],
};

export function Header() {
  const { user, isAuthenticated, clearCredentials, initializeAuth } = useAuthStore();
  const [activeMenu, setActiveMenu] = React.useState<string | null>(null);
  const [isProfileOpen, setIsProfileOpen] = React.useState(false);
  const [isMobileMenuOpen, setIsMobileMenuOpen] = React.useState(false);
  const [navItems, setNavItems] = React.useState<any[]>([]);

  React.useEffect(() => {
    initializeAuth();
  }, [initializeAuth]);

  const cart = useCartStore((state) => state.cart);
  const wishlist = useCartStore((state) => state.wishlist);
  const cartCount = cart.reduce((total, item) => total + item.quantity, 0);
  const wishlistCount = wishlist.length;

  const [mounted, setMounted] = React.useState(false);
  React.useEffect(() => {
    setMounted(true);
  }, []);

  const displayCartCount = mounted ? cartCount : 0;
  const displayWishlistCount = mounted ? wishlistCount : 0;

  const [siteName, setSiteName] = React.useState("AURA");
  const API_URL = process.env.NEXT_PUBLIC_API_URL || "http://localhost:8000";

  React.useEffect(() => {
    fetch(`${API_URL}/api/v1/settings?nocache=1`)
      .then((res) => res.json())
      .then((data) => {
        if (data.site_name) {
          setSiteName(data.site_name);
        }
      })
      .catch(() => {});
  }, [API_URL]);

  // Only these 6 nav categories - in this exact order
  const ALLOWED_NAV = [
    { key: "men",         label: "Men",         slug: "men" },
    { key: "women",       label: "Women",       slug: "women" },
    { key: "kids",        label: "Kids",        slug: "kids" },
    { key: "home",        label: "Home",        slug: "home-living" },
    { key: "beauty",      label: "Beauty",      slug: "beauty" },
    { key: "genz",        label: "GenZ",        slug: "genz" },
  ];

  React.useEffect(() => {
    fetch(`${API_URL}/api/v1/header`)
      .then((res) => {
        if (!res.ok) throw new Error();
        return res.json();
      })
      .then((data) => {
        // Build the 6 nav items from API navigation, filtered to ALLOWED_NAV only
        const apiNav: any[] = data.navigation || [];
        const filtered = ALLOWED_NAV.map(nav => {
          // Find matching item from API by slug or title
          const found = apiNav.find(
            (n: any) =>
              n.url?.toLowerCase().includes(nav.slug) ||
              n.title?.toLowerCase().replace(/[^a-z]/g, "").includes(nav.key.replace(/[^a-z]/g, ""))
          );
          return {
            title: nav.label,
            url: `/catalog/?category=${nav.slug}`,
            type: "category",
            children: found?.children || [],
            _key: nav.key,
          };
        });
        setNavItems(filtered);
      })
      .catch(() => {
        // Fallback: just the 6 keys with no children (megaMenuData used as fallback in dropdown)
        setNavItems(ALLOWED_NAV.map(nav => ({
          title: nav.label,
          url: `/catalog/?category=${nav.slug}`,
          type: "category",
          children: [],
          _key: nav.key,
        })));
      });
  }, [API_URL]);

  return (
    <header className="sticky top-0 z-50 w-full border-b border-border bg-background/95 backdrop-blur-md">
      <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div className="flex h-20 items-center justify-between gap-2 sm:gap-4">
          
          {/* Mobile Drawer */}
          <div className="flex lg:hidden">
            <Sheet open={isMobileMenuOpen} onOpenChange={setIsMobileMenuOpen}>
              <SheetTrigger
                render={
                  <Button variant="ghost" size="icon" className="text-foreground" />
                }
              >
                <Menu className="h-5 w-5" />
                <span className="sr-only">Toggle menu</span>
              </SheetTrigger>
              <SheetContent side="left" className="w-[300px]">
                <nav className="mt-8 flex flex-col gap-4">
                  {navItems.map((item) => (
                    <div key={item.title}>
                      <Link
                        href={getRelativePath(item.url)}
                        onClick={() => setIsMobileMenuOpen(false)}
                        className="text-sm font-black uppercase tracking-wider text-foreground hover:text-[#f51c50] transition-colors"
                      >
                        {item.title}
                      </Link>
                      {/* Mobile sub-items */}
                      {(item.children?.length > 0 || megaMenuData[(item as any)._key ?? item.title.toLowerCase()]) && (
                        <div className="ml-4 mt-2 flex flex-col gap-1.5">
                          {(item.children?.length > 0
                            ? item.children.slice(0, 3)
                            : (megaMenuData[(item as any)._key ?? item.title.toLowerCase()] || []).slice(0, 3)
                          ).map((col: any, ci: number) => (
                            <div key={ci}>
                              <span className="text-[10px] font-black uppercase text-[#f51c50] tracking-wide">
                                {col.title}
                              </span>
                            </div>
                          ))}
                        </div>
                      )}
                    </div>
                  ))}
                  <div className="border-t border-zinc-100 pt-4 mt-2">
                    <Link href={getRelativePath("/login")} onClick={() => setIsMobileMenuOpen(false)}
                      className="text-sm font-black uppercase tracking-wider text-foreground hover:text-[#f51c50] transition-colors">
                      Login / Signup
                    </Link>
                  </div>
                </nav>
              </SheetContent>
            </Sheet>
          </div>

          {/* Logo */}
          <div className="flex flex-none">
            <Link
              href={getRelativePath("/")}
              className="font-heading text-2xl font-extrabold tracking-widest text-[#f51c50] hover:opacity-90 transition-opacity"
            >
              AURA
            </Link>
          </div>

          {/* Categories Navbar Navigation */}
          <nav className="hidden lg:flex lg:gap-4 xl:gap-6 h-full items-center ml-8">
            {navItems.map((item) => {
              const menuKey = item.title.toLowerCase();
              return (
                <div
                  key={item.title}
                  className="h-full flex items-center"
                  onMouseEnter={() => setActiveMenu(menuKey)}
                  onMouseLeave={() => setActiveMenu(null)}
                >
                  <Link
                    href={getRelativePath(item.url)}
                    className={`text-[12px] font-bold uppercase tracking-widest px-2 py-2 border-b-4 transition-colors ${
                      activeMenu === menuKey ? "border-[#f51c50] text-[#f51c50]" : "border-transparent text-foreground"
                    }`}
                  >
                    {item.title}
                  </Link>
                </div>
              );
            })}
          </nav>

          {/* Wide Search Bar & Action Buttons */}
          <div className="flex flex-1 items-center justify-end gap-4 sm:gap-6 ml-2 sm:ml-4">
            
            <div className="relative hidden max-w-2xl w-full lg:block flex-1">
              <Search className="absolute left-3 top-3.5 h-4 w-4 text-muted-foreground" />
              <Input
                type="search"
                placeholder="Search for products, brands and more"
                className="w-full pl-10 pr-4 py-6 text-xs bg-muted/40 border-none rounded-md placeholder-muted-foreground focus:bg-background focus:ring-1 focus:ring-[#f51c50] transition-all duration-200"
              />
            </div>

            <div className="flex items-center gap-4 sm:gap-6 flex-none">
              
              {/* Profile Menu Trigger */}
              <div
                className="relative flex flex-col items-center cursor-pointer py-2 group"
                onMouseEnter={() => setIsProfileOpen(true)}
                onMouseLeave={() => setIsProfileOpen(false)}
              >
                <User className="h-5 w-5 text-foreground group-hover:text-[#f51c50] transition-colors" />
                <span className="text-[10px] font-bold mt-1 text-foreground group-hover:text-[#f51c50] transition-colors">
                  Profile
                </span>

                {/* Dropdown Card Clone */}
                {isProfileOpen && (
                  <div className="absolute right-0 top-full mt-2 w-[270px] bg-white border border-zinc-200 rounded-sm shadow-xl p-5 z-50 text-zinc-800 transition-all duration-200">
                    {!isAuthenticated ? (
                      <div className="pb-4 border-b border-zinc-100 mb-3 text-left">
                        <h4 className="font-extrabold text-sm text-zinc-900 leading-none">Welcome</h4>
                        <p className="text-[11px] text-zinc-400 font-bold mt-1.5 leading-none">To access account and manage orders</p>
                        <Link href={getRelativePath("/login")} className="block mt-4">
                          <span className="w-full py-2.5 text-center block text-xs font-black border border-zinc-200 text-[#ff3f6c] hover:bg-[#ff3f6c]/5 rounded-xs uppercase tracking-wider transition-all cursor-pointer">
                            LOGIN / SIGNUP
                          </span>
                        </Link>
                      </div>
                    ) : (
                      <div className="pb-4 border-b border-zinc-100 mb-3 text-left">
                        <h4 className="font-extrabold text-sm text-zinc-900 leading-none">Hello, {user?.name}</h4>
                        <p className="text-[11px] text-zinc-400 font-bold mt-1.5 leading-none">{user?.email}</p>
                        <button
                          onClick={clearCredentials}
                          className="w-full mt-4 py-2.5 text-center block text-xs font-black border border-red-200 text-red-600 hover:bg-red-50 rounded-xs uppercase tracking-wider transition-all cursor-pointer"
                        >
                          LOGOUT
                        </button>
                      </div>
                    )}

                    {/* Section 1: Orders and Insider */}
                    <div className="flex flex-col gap-3 text-left text-xs font-semibold py-1.5 text-zinc-600">
                      <Link href={getRelativePath("/profile/orders")} className="hover:text-zinc-950 hover:font-bold transition-colors">Orders</Link>
                      <Link href={getRelativePath("/profile/wishlist")} className="hover:text-zinc-950 hover:font-bold transition-colors">Wishlist</Link>
                      <Link href={getRelativePath("/gift-cards")} className="hover:text-zinc-950 hover:font-bold transition-colors">Gift Cards</Link>
                      <Link href={getRelativePath("/contact")} className="hover:text-zinc-950 hover:font-bold transition-colors">Contact Us</Link>
                      <div className="flex items-center justify-between">
                        <Link href={getRelativePath("/insider")} className="hover:text-zinc-950 hover:font-bold transition-colors">{siteName} Insider</Link>
                        <span className="bg-[#ff3f6c] text-white text-[8px] font-black italic px-1.5 py-0.5 rounded-xs uppercase tracking-wider scale-90">New</span>
                      </div>
                    </div>

                    {/* Section 2: Cards and Credits */}
                    <div className="border-t border-zinc-100 mt-3 pt-3 flex flex-col gap-3 text-left text-xs font-semibold text-zinc-500">
                      <Link href={getRelativePath("/profile/credit")} className="hover:text-zinc-950 hover:font-bold transition-colors">{siteName} Credit</Link>
                      <Link href={getRelativePath("/profile/coupons")} className="hover:text-zinc-950 hover:font-bold transition-colors">Coupons</Link>
                      <Link href={getRelativePath("/profile/cards")} className="hover:text-zinc-950 hover:font-bold transition-colors">Saved Cards</Link>
                      <Link href={getRelativePath("/profile/vpa")} className="hover:text-zinc-950 hover:font-bold transition-colors">Saved VPA</Link>
                      <Link href={getRelativePath("/profile/addresses")} className="hover:text-zinc-950 hover:font-bold transition-colors">Saved Addresses</Link>
                    </div>
                  </div>
                )}
              </div>

              {/* Wishlist */}
              <Link href={getRelativePath("/profile/wishlist")} className="flex flex-col items-center group relative">
                <Heart className="h-5 w-5 text-foreground group-hover:text-[#f51c50] transition-colors" />
                {displayWishlistCount > 0 && (
                  <span className="absolute -top-1 -right-1.5 flex h-4 w-4 items-center justify-center rounded-full bg-[#f51c50] text-[8px] font-bold text-white">
                    {displayWishlistCount}
                  </span>
                )}
                <span className="text-[10px] font-bold mt-1 text-foreground group-hover:text-[#f51c50] transition-colors">
                  Wishlist
                </span>
              </Link>

              {/* Shopping Bag */}
              <Link href={getRelativePath("/checkout/cart")} className="flex flex-col items-center cursor-pointer group relative">
                <ShoppingBag className="h-5 w-5 text-foreground group-hover:text-[#f51c50] transition-colors" />
                {displayCartCount > 0 && (
                  <span className="absolute -top-1 -right-1.5 flex h-4 w-4 items-center justify-center rounded-full bg-[#f51c50] text-[8px] font-bold text-white">
                    {displayCartCount}
                  </span>
                )}
                <span className="text-[10px] font-bold mt-1 text-foreground group-hover:text-[#f51c50] transition-colors">
                  Bag
                </span>
              </Link>

            </div>
          </div>
        </div>
      </div>

      {/* Hover Dropdowns */}
      {activeMenu && (
        <div
          className="absolute left-0 right-0 top-full z-40 border-b border-border bg-background shadow-xl transition-all duration-200"
          onMouseEnter={() => setActiveMenu(activeMenu)}
          onMouseLeave={() => setActiveMenu(null)}
        >
          {activeMenu === "studio" ? (
            /* Custom Studio Lookbook layout */
            <div className="mx-auto max-w-lg bg-background p-6 flex flex-col items-center select-none">
              <div className="flex items-center gap-2">
                <div className="h-6 w-6 rounded-md bg-[#f51c50] flex items-center justify-center text-white font-black text-xs">M</div>
                <h3 className="font-heading text-lg font-black tracking-widest text-foreground">Studio</h3>
              </div>
              <p className="text-[11px] font-semibold text-muted-foreground mt-1 text-center tracking-wide">
                Your daily inspiration for everything fashion
              </p>
              
              <div className="grid grid-cols-4 gap-2 w-full mt-4 h-[240px] rounded-sm overflow-hidden border border-border">
                <div className="relative h-full w-full">
                  <Image
                    src="https://images.unsplash.com/photo-1500648767791-00dcc994a43e?q=80&w=300&auto=format&fit=crop"
                    alt="Studio Style 1"
                    fill
                    className="object-cover"
                  />
                </div>
                <div className="relative h-full w-full">
                  <Image
                    src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?q=80&w=300&auto=format&fit=crop"
                    alt="Studio Style 2"
                    fill
                    className="object-cover"
                  />
                </div>
                <div className="relative h-full w-full">
                  <Image
                    src="https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?q=80&w=300&auto=format&fit=crop"
                    alt="Studio Style 3"
                    fill
                    className="object-cover"
                  />
                </div>
                <div className="relative h-full w-full">
                  <Image
                    src="https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?q=80&w=300&auto=format&fit=crop"
                    alt="Studio Style 4"
                    fill
                    className="object-cover"
                  />
                </div>
              </div>

              <Link href={getRelativePath("/catalog/?category=studio")} className="block mt-5 w-full max-w-[200px]">
                <Button variant="outline" className="w-full text-[10px] font-black uppercase tracking-widest border-border py-5 rounded-sm hover:bg-zinc-55 transition-colors">
                  EXPLORE STUDIO &gt;
                </Button>
              </Link>
            </div>
            ) : (
              (() => {
                const menuKey = activeMenu ?? "";
                const currentItem = navItems.find(i => i.title.toLowerCase() === menuKey);
                // _key is the megaMenuData key (e.g. "home" maps to megaMenuData["home"])
                const dataKey: string = (currentItem as any)?._key ?? menuKey;
                const apiChildren: any[] = currentItem?.children || [];

                // Build columns: use API children if available, else fall back to megaMenuData
                const hasApiCols = apiChildren.length > 0 && apiChildren.some((c: any) => c.title);
                const cols: MegaMenuColumn[] = hasApiCols
                  ? apiChildren.map((c: any) => ({
                      title: c.title,
                      url:   c.url ?? `/catalog/?category=${encodeURIComponent((c.title ?? "").toLowerCase())}`,
                      items: (c.items || c.children || [])
                        .slice(0, 3)
                        .map((s: any) =>
                          typeof s === "string"
                            ? {
                                title: s,
                                url: `/catalog/?category=${encodeURIComponent(s.toLowerCase())}`,
                              }
                            : {
                                title: s.title,
                                url:
                                  s.url ??
                                  `/catalog/?category=${encodeURIComponent(
                                    (s.title ?? "").toLowerCase()
                                  )}`,
                              }
                        ),
                    }))
                  : (megaMenuData[dataKey] || []).map(col => ({
                      title: col.title,
                      url:   `/catalog/?category=${encodeURIComponent(col.title.toLowerCase())}`,
                      items: col.items.slice(0, 3).map(item => ({
                        title: item,
                        url:   `/catalog/?category=${encodeURIComponent(item.toLowerCase())}`,
                      })),
                    }));

                if (!cols.length) return null;

                const colClass = cols.length <= 3 ? "grid-cols-3" : cols.length === 4 ? "grid-cols-4" : "grid-cols-5";
                const headColor = dataKey === "genz" ? "text-[#00a896]" : "text-[#f51c50]";

                return (
                  <div className="mx-auto max-w-7xl px-8 py-8">
                    <div className={`grid ${colClass} gap-8`}>
                      {cols.map((col, idx) => (
                        <div key={idx} className="flex flex-col gap-3">
                          <Link href={getRelativePath(col.url)}>
                            <h4 className={`font-heading text-xs font-black uppercase tracking-wider cursor-pointer hover:underline ${headColor}`}>
                              {col.title}
                            </h4>
                          </Link>
                          <ul className="space-y-1.5">
                           {col.items.map((sub: MegaMenuItem, si: number) => (
                              <li key={si}>
                                <Link
                                  href={getRelativePath(sub.url)}
                                  className="text-xs text-zinc-600 hover:text-black font-medium transition-colors leading-relaxed"
                                >
                                  {sub.title}
                                </Link>
                              </li>
                            ))}
                          </ul>
                        </div>
                      ))}
                    </div>
                  </div>
                );
              })()
            )}
        </div>
      )}
    </header>
  );
}
