"use client";

import * as React from "react";
import Link from "next/link";
import { usePathname, useRouter } from "next/navigation";
import { useAuthStore } from "@/store/slices/authSlice";
import { getRelativePath } from "@/lib/utils";
import { ShoppingBag, Heart, CreditCard, Tag, MapPin, User, LogOut, Wallet } from "lucide-react";

export default function ProfileLayout({ children }: { children: React.ReactNode }) {
  const pathname = usePathname();
  const router = useRouter();
  const { user, isAuthenticated, initializeAuth, clearCredentials } = useAuthStore();
  const [siteName, setSiteName] = React.useState("AURA");

  const API_URL = process.env.NEXT_PUBLIC_API_URL || "http://localhost:8000";

  React.useEffect(() => {
    initializeAuth();
  }, [initializeAuth]);

  React.useEffect(() => {
    if (!isAuthenticated) {
      router.push("/login");
    }
  }, [isAuthenticated, router]);

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

  const menuItems = [
    { name: "Orders", path: "/profile/orders", icon: ShoppingBag },
    { name: "Wishlist", path: "/profile/wishlist", icon: Heart },
    { name: `${siteName} Credit`, path: "/profile/credit", icon: Wallet },
    { name: "Coupons", path: "/profile/coupons", icon: Tag },
    { name: "Saved Addresses", path: "/profile/addresses", icon: MapPin },
    { name: "Saved Cards", path: "/profile/cards", icon: CreditCard },
    { name: "Saved VPA", path: "/profile/vpa", icon: CreditCard },
  ];

  if (!isAuthenticated) {
    return (
      <div className="min-h-screen bg-zinc-50 flex items-center justify-center">
        <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-[#ff3f6c]"></div>
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-zinc-50/50 py-10 px-4 sm:px-6 lg:px-8 select-none">
      <div className="max-w-6xl mx-auto flex flex-col md:flex-row gap-8">
        
        {/* Sidebar */}
        <aside className="w-full md:w-64 shrink-0 bg-white border border-zinc-150 rounded-xl p-5 shadow-3xs flex flex-col gap-6">
          
          {/* User profile brief card */}
          <div className="flex items-center gap-3 pb-5 border-b border-zinc-100">
            <div className="h-10 w-10 bg-[#ff3f6c]/5 border border-[#ff3f6c]/15 text-[#ff3f6c] font-black rounded-full flex items-center justify-center text-sm uppercase">
              {user?.name?.substring(0, 2) || "C"}
            </div>
            <div className="overflow-hidden">
              <h3 className="font-extrabold text-xs text-zinc-800 truncate">{user?.name}</h3>
              <p className="text-[10px] text-zinc-400 font-bold truncate mt-0.5">{user?.email}</p>
            </div>
          </div>

          {/* Navigation Links */}
          <nav className="flex flex-col gap-1">
            {menuItems.map((item) => {
              const isActive = pathname === item.path;
              const Icon = item.icon;
              return (
                <Link
                  key={item.path}
                  href={getRelativePath(item.path)}
                  className={`flex items-center gap-3 px-3 py-2.5 rounded-lg text-xs font-bold transition-all ${
                    isActive
                      ? "bg-[#ff3f6c]/5 text-[#ff3f6c] border-l-2 border-[#ff3f6c]"
                      : "text-zinc-650 hover:bg-zinc-50 hover:text-zinc-950"
                  }`}
                >
                  <Icon className={`h-4 w-4 ${isActive ? "text-[#ff3f6c]" : "text-zinc-400"}`} />
                  {item.name}
                </Link>
              );
            })}

            <button
              onClick={() => {
                clearCredentials();
                router.push("/login");
              }}
              className="flex items-center gap-3 px-3 py-2.5 rounded-lg text-xs font-bold text-red-650 hover:bg-red-50 transition-all text-left mt-4 border-t border-zinc-100 pt-4"
            >
              <LogOut className="h-4 w-4 text-red-450" />
              Log Out
            </button>
          </nav>
        </aside>

        {/* Content area */}
        <main className="flex-grow bg-white border border-zinc-150 rounded-xl p-6 sm:p-8 shadow-3xs min-h-[480px]">
          {children}
        </main>

      </div>
    </div>
  );
}
