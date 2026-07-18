"use client";

import * as React from "react";
import Link from "next/link";
import Image from "next/image";
import { getRelativePath } from "@/lib/utils";
import { useCartStore } from "@/store/useCartStore";
import { Trash2, ShoppingBag, Heart, ArrowRight } from "lucide-react";
import { Button } from "@/components/ui/button";
import { toast } from "sonner";

export default function WishlistPage() {
  const wishlist = useCartStore((state) => state.wishlist);
  const toggleWishlist = useCartStore((state) => state.toggleWishlist);

  const [mounted, setMounted] = React.useState(false);

  React.useEffect(() => {
    setMounted(true);
  }, []);

  const handleRemove = (productId: number, title: string) => {
    toggleWishlist({
      productId,
      title,
      brand: "",
      image: "",
      price: 0,
    });

    toast.info("Removed from wishlist", {
      description: `${title} has been removed.`,
    });
  };

  if (!mounted) {
    return (
      <div className="mx-auto max-w-7xl px-4 py-20 text-center text-zinc-400 font-bold uppercase tracking-widest text-xs animate-pulse">
        Loading Wishlist...
      </div>
    );
  }

  return (
    <div className="text-zinc-800 text-left">
      <div className="flex items-center gap-3 mb-6 pb-4 border-b border-zinc-100">
        <Heart className="h-6 w-6 text-[#f51c50] fill-[#f51c50]" />

        <h1 className="text-lg font-black uppercase tracking-wider text-zinc-950">
          My Wishlist ({wishlist.length})
        </h1>
      </div>

      {wishlist.length === 0 ? (
        <div className="flex flex-col items-center justify-center py-20 bg-zinc-50/50 border border-zinc-200 rounded-2xl p-8 max-w-lg mx-auto shadow-sm select-none">
          <div className="h-16 w-16 bg-[#f51c50]/5 rounded-full flex items-center justify-center mb-6">
            <Heart className="h-8 w-8 text-[#f51c50]" />
          </div>

          <h2 className="text-sm font-black uppercase tracking-wider text-zinc-900">
            Your Wishlist is Empty
          </h2>

          <p className="text-[11px] text-zinc-500 font-semibold text-center mt-2 max-w-xs leading-relaxed">
            Discover new trends, explore collections, and save your favorite
            outfits here.
          </p>

          <Link
            href={getRelativePath("/catalog/")}
            className="mt-6 w-full"
          >
            <Button className="w-full bg-[#f51c50] hover:bg-[#f51c50]/90 text-white font-black text-xs uppercase tracking-widest py-5 rounded-xl gap-2">
              Start Shopping
              <ArrowRight className="h-4 w-4" />
            </Button>
          </Link>
        </div>
      ) : (
        <div className="grid grid-cols-2 lg:grid-cols-3 gap-6">
          {wishlist.map((item) => (
            <div
              key={item.productId}
              className="group flex flex-col bg-white border border-zinc-200 rounded-2xl overflow-hidden shadow-sm hover:shadow-lg transition-all duration-300 relative"
            >
              <button
                onClick={() => handleRemove(item.productId, item.title)}
                className="absolute top-3 right-3 z-20 bg-white/90 backdrop-blur text-zinc-500 hover:text-[#f51c50] p-2 rounded-full shadow-sm transition"
              >
                <Trash2 className="h-4 w-4" />
              </button>

              <Link
                href={getRelativePath(
                  `/product/?id=aura-designer-suit-${item.productId}-${item.productId}`
                )}
                className="flex flex-col flex-grow"
              >
                <div className="relative aspect-[3/4] w-full bg-zinc-50 overflow-hidden">
                  <Image
                    src={
                      item.image ||
                      "https://images.unsplash.com/photo-1595950653106-6c9ebd614d3a?q=80&w=300&auto=format&fit=crop"
                    }
                    alt={item.title}
                    fill
                    sizes="(max-width:768px) 50vw, 20vw"
                    className="object-cover transition-transform duration-500 group-hover:scale-105"
                  />
                </div>

                <div className="p-4 flex flex-col flex-grow">
                  <span className="text-[10px] font-black uppercase tracking-widest text-[#f51c50]">
                    {item.brand}
                  </span>

                  <h3 className="text-xs text-zinc-500 font-bold truncate mt-1">
                    {item.title}
                  </h3>

                  <div className="mt-3 text-[11px] font-black text-zinc-900">
                    Rs. {item.price}
                  </div>
                </div>
              </Link>

              <div className="p-3 border-t border-zinc-100 bg-zinc-50">
                <Link
                  href={getRelativePath(
                    `/product/?id=aura-designer-suit-${item.productId}-${item.productId}`
                  )}
                >
                  <Button className="w-full bg-[#f51c50] hover:bg-[#f51c50]/90 text-white font-black text-[10px] uppercase tracking-widest py-4 rounded-xl gap-2">
                    <ShoppingBag className="h-4 w-4" />
                    Select & Add to Bag
                  </Button>
                </Link>
              </div>
            </div>
          ))}
        </div>
      )}
    </div>
  );
}