"use client";

import * as React from "react";
import Link from "next/link";
import Image from "next/image";
import { useSearchParams } from "next/navigation";
import { getRelativePath } from "@/lib/utils";
import { Star, ChevronRight, Heart } from "lucide-react";
import { Checkbox } from "@/components/ui/checkbox";
import { RadioGroup, RadioGroupItem } from "@/components/ui/radio-group";

interface ProductItem {
  id: number;
  title: string;
  slug: string;
  sku: string;
  price: number;
  brand?: {
    name: string;
  };
  label?: {
    name: string;
    bg_color: string;
    text_color: string;
  };
  media: { path: string }[];
  variants: { price: number; sale_price: number | null }[];
}

function CatalogContent() {
  const [products, setProducts] = React.useState<ProductItem[]>([]);
  const [filters, setFilters] = React.useState<any[]>([]);
  const [pagination, setPagination] = React.useState<any>(null);

  // Search parameters for dynamic categories filter context
  const searchParams = useSearchParams();
  const categoryParam = searchParams.get("category") || "";

  const categoryTitle = React.useMemo(() => {
    if (!categoryParam) return "All Collections";
    return categoryParam
      .split("-")
      .map((word) => word.charAt(0).toUpperCase() + word.slice(1))
      .join(" ");
  }, [categoryParam]);

  // Filter States
  const [selectedBrands, setSelectedBrands] = React.useState<string[]>([]);
  const [selectedSizes, setSelectedSizes] = React.useState<string[]>([]);
  const [selectedColors, setSelectedColors] = React.useState<string[]>([]);
  const [selectedPrice, setSelectedPrice] = React.useState<string>("");
  const [sort, setSort] = React.useState<string>("recommended");

  const API_URL = process.env.NEXT_PUBLIC_API_URL || "http://localhost:8000";

  // Reset filters when changing category parameter context
  React.useEffect(() => {
    setSelectedBrands([]);
    setSelectedSizes([]);
    setSelectedColors([]);
    setSelectedPrice("");
  }, [categoryParam]);

  // Fetch products with active configurations
  const fetchProducts = React.useCallback(() => {
    const params = new URLSearchParams();
    if (categoryParam) {
      params.append("category", categoryParam);
    }
    if (selectedBrands.length > 0) params.append("brand", selectedBrands.join(","));
    if (selectedSizes.length > 0) params.append("size", selectedSizes.join(","));
    if (selectedColors.length > 0) params.append("color", selectedColors.join(","));
    if (selectedPrice) params.append("price", selectedPrice);
    params.append("sort", sort);

    fetch(`${API_URL}/api/v1/products?${params.toString()}`)
      .then((res) => res.json())
      .then((data) => {
        setProducts(data.products || []);
        if (data.filters) setFilters(data.filters);
        setPagination(data.pagination);
      })
      .catch(() => {
        setProducts([]);
      });
  }, [selectedBrands, selectedSizes, selectedColors, selectedPrice, sort, categoryParam, API_URL]);

  React.useEffect(() => {
    fetchProducts();
  }, [fetchProducts]);

  // Wishlist handler toggle
  const toggleWishlist = (productId: number) => {
    fetch(`${API_URL}/api/v1/wishlist`, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ product_id: productId, session_key: "guest_session_1" }),
    })
      .then((res) => res.json())
      .then((data) => {
        alert(`Product successfully ${data.status} to Wishlist!`);
      });
  };

  const handleBrandChange = (slug: string, checked: boolean) => {
    setSelectedBrands((prev) =>
      checked ? [...prev, slug] : prev.filter((s) => s !== slug)
    );
  };

  const handleSizeChange = (name: string, checked: boolean) => {
    setSelectedSizes((prev) =>
      checked ? [...prev, name] : prev.filter((s) => s !== name)
    );
  };

  const handleColorChange = (name: string, checked: boolean) => {
    setSelectedColors((prev) =>
      checked ? [...prev, name] : prev.filter((s) => s !== name)
    );
  };

  return (
    <div className="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8 bg-white min-h-screen text-zinc-800">
      
      {/* Breadcrumb Section */}
      <div className="flex items-center gap-1.5 text-[11px] text-zinc-500 font-medium select-none">
        <Link href={getRelativePath("/")} className="hover:text-black">Home</Link>
        <ChevronRight className="h-3 w-3" />
        <Link href={getRelativePath("/catalog/")} className="hover:text-black">Catalog</Link>
        <ChevronRight className="h-3 w-3" />
        <span className="font-bold text-zinc-800">{categoryTitle}</span>
      </div>

      {/* Main Title Banner */}
      <div className="mt-4 pb-4 border-b border-zinc-200/80">
        <h1 className="text-sm font-black uppercase tracking-wider text-zinc-900">
          {categoryTitle} <span className="text-xs text-zinc-400 font-normal ml-1">({pagination?.total || products.length} items)</span>
        </h1>
      </div>

      <div className="flex gap-6 mt-6">
        
        {/* Left Side Dynamic Filters Sidebar */}
        <aside className="w-[240px] flex-none hidden md:block border-r border-zinc-200 pr-6 select-none">
          <div className="flex items-center justify-between pb-3 mb-4 border-b border-zinc-200">
            <span className="text-xs font-black uppercase tracking-wider text-zinc-900">Filters</span>
            <button
              onClick={() => {
                setSelectedBrands([]);
                setSelectedSizes([]);
                setSelectedColors([]);
                setSelectedPrice("");
              }}
              className="text-[10px] font-bold text-[#f51c50] uppercase tracking-wider hover:opacity-90"
            >
              Clear All
            </button>
          </div>

          {filters.map((filter) => {
            if (filter.key === "brand") {
              return (
                <div key={filter.key} className="mb-6">
                  <h3 className="text-xs font-black uppercase tracking-wider text-zinc-800 mb-3">{filter.label}</h3>
                  <div className="flex flex-col gap-2">
                    {Array.isArray(filter.options) && filter.options.map((opt: any) => (
                      <label key={opt.value} className="flex items-center gap-2 text-xs font-medium cursor-pointer text-zinc-600 hover:text-black">
                        <Checkbox
                          checked={selectedBrands.includes(opt.value)}
                          onCheckedChange={(checked) => handleBrandChange(opt.value, !!checked)}
                          className="border-zinc-300 rounded-sm data-[state=checked]:bg-[#f51c50] data-[state=checked]:border-[#f51c50]"
                        />
                        <span>{opt.label}</span>
                      </label>
                    ))}
                  </div>
                </div>
              );
            }

            if (filter.key === "size") {
              return (
                <div key={filter.key} className="mb-6 border-t border-zinc-100 pt-4">
                  <h3 className="text-xs font-black uppercase tracking-wider text-zinc-800 mb-3">{filter.label}</h3>
                  <div className="flex flex-col gap-2">
                    {Array.isArray(filter.options) && filter.options.map((opt: any) => (
                      <label key={opt.value} className="flex items-center gap-2 text-xs font-medium cursor-pointer text-zinc-600 hover:text-black">
                        <Checkbox
                          checked={selectedSizes.includes(opt.value)}
                          onCheckedChange={(checked) => handleSizeChange(opt.value, !!checked)}
                          className="border-zinc-300 rounded-sm data-[state=checked]:bg-[#f51c50] data-[state=checked]:border-[#f51c50]"
                        />
                        <span>{opt.label}</span>
                      </label>
                    ))}
                  </div>
                </div>
              );
            }

            if (filter.key === "color") {
              return (
                <div key={filter.key} className="mb-6 border-t border-zinc-100 pt-4">
                  <h3 className="text-xs font-black uppercase tracking-wider text-zinc-800 mb-3">{filter.label}</h3>
                  <div className="flex flex-col gap-2">
                    {Array.isArray(filter.options) && filter.options.map((opt: any) => (
                      <label key={opt.value} className="flex items-center gap-2 text-xs font-medium cursor-pointer text-zinc-600 hover:text-black">
                        <Checkbox
                          checked={selectedColors.includes(opt.value)}
                          onCheckedChange={(checked) => handleColorChange(opt.value, !!checked)}
                          className="border-zinc-300 rounded-sm data-[state=checked]:bg-[#f51c50] data-[state=checked]:border-[#f51c50]"
                        />
                        <span className="w-3 h-3 rounded-full border border-zinc-200" style={{ backgroundColor: opt.hex }} />
                        <span>{opt.label}</span>
                      </label>
                    ))}
                  </div>
                </div>
              );
            }

            if (filter.key === "price") {
              return (
                <div key={filter.key} className="mb-6 border-t border-zinc-100 pt-4">
                  <h3 className="text-xs font-black uppercase tracking-wider text-zinc-800 mb-3">{filter.label}</h3>
                  <RadioGroup value={selectedPrice} onValueChange={setSelectedPrice} className="flex flex-col gap-2 mt-2">
                    {Array.isArray(filter.options) && filter.options.map((opt: any) => (
                      <div key={opt.value} className="flex items-center gap-2">
                        <RadioGroupItem value={opt.value} id={opt.value} className="border-zinc-300 text-[#f51c50] focus:ring-[#f51c50]" />
                        <label htmlFor={opt.value} className="text-xs font-medium text-zinc-600 hover:text-black cursor-pointer">
                          {opt.label}
                        </label>
                      </div>
                    ))}
                  </RadioGroup>
                </div>
              );
            }

            return null;
          })}
        </aside>

        {/* Right Side Products Grid Area */}
        <main className="flex-grow">
          
          {/* Sorting Row */}
          <div className="flex justify-end items-center pb-4 mb-6 border-b border-zinc-100 select-none">
            <div className="relative">
              <select
                value={sort}
                onChange={(e) => setSort(e.target.value)}
                className="text-xs font-semibold bg-white border border-zinc-200 rounded-sm px-3 py-1.5 focus:outline-none focus:ring-1 focus:ring-[#f51c50] cursor-pointer"
              >
                <option value="recommended">Sort by: Recommended</option>
                <option value="price_asc">Price: Low to High</option>
                <option value="price_desc">Price: High to Low</option>
                <option value="newest">Newest Arrivals</option>
                <option value="rating">Highest Rated</option>
              </select>
            </div>
          </div>

          {/* Products Grid */}
          {products.length === 0 ? (
            <div className="text-center py-20 text-zinc-400 font-medium">
              No products match selected filters. Please adjust settings.
            </div>
          ) : (
            <div className="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-x-6 gap-y-10">
              {products.map((prod) => {
                const mainImg = prod.media?.[0]?.path || "https://images.unsplash.com/photo-1542291026-7eec264c27ff?q=80&w=400&auto=format&fit=crop";
                const displayPrice = prod.variants?.[0]?.price || prod.price;
                const salePrice = prod.variants?.[0]?.sale_price;

                return (
                  <div key={prod.id} className="group relative flex flex-col cursor-pointer bg-white">
                    
                    {/* Image Box */}
                    <div className="relative aspect-3/4 w-full bg-zinc-100 overflow-hidden rounded-xs">
                      <Link href={getRelativePath(`/product/?id=${prod.slug}`)} className="block w-full h-full">
                        <Image
                          src={mainImg}
                          alt={prod.title}
                          fill
                          sizes="(max-width: 768px) 50vw, 20vw"
                          className="object-cover transition-transform duration-500 group-hover:scale-103"
                        />
                      </Link>

                      {/* Label badge overlay */}
                      {prod.label && (
                        <span
                          className="absolute top-2.5 left-2.5 text-[8px] font-black px-2 py-0.5 rounded-xs uppercase tracking-wider"
                          style={{ backgroundColor: prod.label.bg_color, color: prod.label.text_color }}
                        >
                          {prod.label.name}
                        </span>
                      )}

                      {/* Wishlist quick click */}
                      <button
                        onClick={() => toggleWishlist(prod.id)}
                        className="absolute top-2.5 right-2.5 opacity-0 group-hover:opacity-100 transition-opacity bg-white p-1.5 rounded-full shadow-md hover:bg-zinc-50 cursor-pointer"
                      >
                        <Heart className="h-3.5 w-3.5 text-zinc-500 hover:text-[#f51c50] transition-colors" />
                      </button>
                    </div>

                    {/* Details */}
                    <div className="mt-3 flex flex-col">
                      <h3 className="text-xs font-black uppercase tracking-wider text-zinc-900 leading-tight">
                        {prod.brand?.name || "AURA BRAND"}
                      </h3>
                      <p className="text-[10.5px] text-zinc-500 mt-0.5 truncate leading-tight">
                        {prod.title}
                      </p>
                      
                      {/* Price Row */}
                      <div className="flex items-center gap-1.5 mt-1.5 text-[11px] font-bold">
                        <span className="text-zinc-900 font-extrabold">Rs. {salePrice || displayPrice}</span>
                        {salePrice && (
                          <>
                            <span className="text-zinc-400 line-through font-normal text-[10px]">Rs. {displayPrice}</span>
                            <span className="text-[#f51c50] text-[9.5px] font-black">
                              ({Math.round(((displayPrice - salePrice) / displayPrice) * 100)}% OFF)
                            </span>
                          </>
                        )}
                      </div>
                    </div>

                  </div>
                );
              })}
            </div>
          )}

        </main>
      </div>

    </div>
  );
}

export default function CatalogPage() {
  return (
    <React.Suspense fallback={<div className="text-center py-20 text-xs font-black uppercase tracking-widest text-[#f51c50]">Loading Catalog...</div>}>
      <CatalogContent />
    </React.Suspense>
  );
}
