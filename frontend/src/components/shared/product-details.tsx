"use client";

import * as React from "react";
import Link from "next/link";
import Image from "next/image";
import { getRelativePath } from "@/lib/utils";
import { useCartStore } from "@/store/useCartStore";
import { toast } from "sonner";
import { 
  Star, 
  Heart, 
  ShoppingBag, 
  MapPin, 
  Truck, 
  ShieldCheck, 
  RotateCcw, 
  BadgePercent, 
  Sparkles,
  ChevronRight,
  MessageSquare,
  Maximize2
} from "lucide-react";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";

interface Variant {
  id: number;
  sku: string;
  price: number;
  sale_price: number | null;
  size_id: number | null;
  color_id: number | null;
  size?: { name: string };
  color?: { name: string };
}

interface Review {
  id: number;
  rating: number;
  title: string | null;
  comment: string;
  is_verified: boolean;
  helpful_votes: number;
  user?: { name: string };
}

interface Product {
  id: number;
  title: string;
  sku: string;
  description: string | null;
  highlights: string[] | null;
  specifications: { name: string; value: string }[] | null;
  origin_country: string | null;
  wash_care: string | null;
  brand?: { name: string };
  media: { path: string; type: string }[];
}

export function ProductDetails({ id }: { id: string }) {
  const [product, setProduct] = React.useState<Product | null>(null);
  const [variants, setVariants] = React.useState<Variant[]>([]);
  const [reviews, setReviews] = React.useState<Review[]>([]);
  const [similar, setSimilar] = React.useState<any[]>([]);
  const [loading, setLoading] = React.useState<boolean>(true);
  const [error, setError] = React.useState<string | null>(null);

  // Selection States
  const [selectedSize, setSelectedSize] = React.useState<string | null>(null);
  const [selectedColor, setSelectedColor] = React.useState<string | null>(null);
  const [activeVariant, setActiveVariant] = React.useState<Variant | null>(null);
  const [activeImageIndex, setActiveImageIndex] = React.useState<number>(0);
  const [activeTab, setActiveTab] = React.useState<"description" | "specifications" | "wash">("description");

  // Lightbox Modal States
  const [isLightboxOpen, setIsLightboxOpen] = React.useState<boolean>(false);
  const [lightboxImageIndex, setLightboxImageIndex] = React.useState<number>(0);

  // Delivery Checker
  const [pincode, setPincode] = React.useState("");
  const [pincodeStatus, setPincodeStatus] = React.useState<string | null>(null);

  // Review Input States
  const [rating, setRating] = React.useState(5);
  const [reviewComment, setReviewComment] = React.useState("");
  const [reviewTitle, setReviewTitle] = React.useState("");

  const API_URL = process.env.NEXT_PUBLIC_API_URL || "http://localhost:8000";

  // Zustand Store selectors
  const addToCart = useCartStore((state) => state.addToCart);
  const toggleWishlist = useCartStore((state) => state.toggleWishlist);
  const wishlist = useCartStore((state) => state.wishlist);

  React.useEffect(() => {
    setLoading(true);
    setError(null);
    setActiveImageIndex(0);
    fetch(`${API_URL}/api/v1/products/${id}`)
      .then((res) => {
        if (!res.ok) {
          throw new Error("Product not found");
        }
        return res.json();
      })
      .then((data) => {
        if (!data || !data.product) {
          throw new Error("Product not found");
        }
        setProduct(data.product);
        setVariants(data.variants || []);
        setReviews(data.reviews || []);
        setSimilar(data.similar || []);

        // Track recently viewed in database
        fetch(`${API_URL}/api/v1/recently-viewed`, {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ product_id: data.product.id, session_key: "guest_session_1" }),
        }).catch(() => {});
        setLoading(false);
      })
      .catch((err) => {
        setError(err.message || "Failed to load product details");
        setLoading(false);
      });
  }, [id, API_URL]);

  // Recalculate variant when size/color changes
  React.useEffect(() => {
    if (selectedSize || selectedColor) {
      const match = variants.find((v) => {
        const sizeMatch = !selectedSize || v.size?.name === selectedSize;
        const colorMatch = !selectedColor || v.color?.name === selectedColor;
        return sizeMatch && colorMatch;
      });
      setActiveVariant(match || null);
    } else {
      setActiveVariant(null);
    }
  }, [selectedSize, selectedColor, variants]);

  // Keyboard Navigation for Lightbox Modal
  React.useEffect(() => {
    if (!isLightboxOpen || !product || !product.media.length) return;

    const handleKeyDown = (e: KeyboardEvent) => {
      if (e.key === "ArrowRight") {
        setLightboxImageIndex((prev) => (prev + 1) % product.media.length);
      } else if (e.key === "ArrowLeft") {
        setLightboxImageIndex((prev) => (prev - 1 + product.media.length) % product.media.length);
      } else if (e.key === "Escape") {
        setIsLightboxOpen(false);
      }
    };

    window.addEventListener("keydown", handleKeyDown);
    return () => window.removeEventListener("keydown", handleKeyDown);
  }, [isLightboxOpen, product]);

  const checkPincode = (e: React.FormEvent) => {
    e.preventDefault();
    if (pincode.length === 6) {
      setPincodeStatus("Available for dispatch within 24 hours.");
    } else {
      setPincodeStatus("Please enter a valid 6-digit PIN.");
    }
  };

  const handlePostReview = (e: React.FormEvent) => {
    e.preventDefault();
    if (!product) return;

    fetch(`${API_URL}/api/v1/reviews`, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        product_id: product.id,
        rating: rating,
        comment: reviewComment,
        title: reviewTitle,
      }),
    })
      .then((res) => res.json())
      .then((data) => {
        if (data.review) {
          setReviews((prev) => [data.review, ...prev]);
          setReviewComment("");
          setReviewTitle("");
          toast.success("Review posted successfully!");
        }
      });
  };

  if (loading) {
    return (
      <div className="mx-auto max-w-7xl px-4 py-32 text-center text-zinc-400 font-bold uppercase tracking-widest text-xs animate-pulse">
        Loading product details...
      </div>
    );
  }

  if (error || !product) {
    return (
      <div className="mx-auto max-w-7xl px-4 py-32 text-center text-[#f51c50] font-black uppercase tracking-widest text-xs">
        {error || "Product not found."}
      </div>
    );
  }

  // Active prices computation
  const displayPrice = activeVariant ? activeVariant.price : (variants[0]?.price || 1999);
  const salePrice = activeVariant ? activeVariant.sale_price : (variants[0]?.sale_price || null);

  // Collect unique sizes and colors from variants
  const uniqueSizes = Array.from(new Set(variants.map((v) => v.size?.name).filter((x): x is string => !!x)));
  const uniqueColors = Array.from(new Set(variants.map((v) => v.color?.name).filter((x): x is string => !!x)));

  const mainImage = product.media[activeImageIndex]?.path || "https://images.unsplash.com/photo-1515886657613-9f3515b0c78f?q=80&w=600&auto=format&fit=crop";
  const isWishlisted = wishlist.some((w) => w.productId === product.id);

  const handleAddToBag = () => {
    if (uniqueColors.length > 0 && !selectedColor) {
      toast.warning("Please select a color first!");
      return;
    }
    if (uniqueSizes.length > 0 && !selectedSize) {
      toast.warning("Please select a size first!");
      return;
    }

    addToCart({
      productId: product.id,
      title: product.title,
      brand: product.brand?.name || "AURA BRAND",
      image: mainImage,
      price: salePrice || displayPrice,
      size: selectedSize,
      color: selectedColor,
    });

    toast.success("Success! Added to Bag", {
      description: `${product.brand?.name || "AURA"} - ${product.title} (Size: ${selectedSize || "N/A"}, Color: ${selectedColor || "N/A"}) has been added.`,
    });
  };

  const handleToggleWishlist = () => {
    toggleWishlist({
      productId: product.id,
      title: product.title,
      brand: product.brand?.name || "AURA BRAND",
      image: mainImage,
      price: salePrice || displayPrice,
    });

    if (isWishlisted) {
      toast.info("Removed from Wishlist", {
        description: `${product.title} has been removed from your wishlist.`,
      });
    } else {
      toast.success("Saved to Wishlist!", {
        description: `${product.title} has been saved to your wishlist.`,
      });
    }
  };

  const openLightbox = (index: number) => {
    setLightboxImageIndex(index);
    setIsLightboxOpen(true);
  };

  return (
    <div className="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8 bg-[#fafafa] min-h-screen text-zinc-800">
      
      {/* Breadcrumbs */}
      <div className="flex items-center gap-1 text-[11px] text-zinc-400 font-bold pb-6 select-none uppercase tracking-wider text-left">
        <Link href={getRelativePath("/")} className="hover:text-black transition-colors">Home</Link>
        <ChevronRight className="h-3 w-3 text-zinc-400" />
        <Link href={getRelativePath("/catalog/")} className="hover:text-black transition-colors">Catalog</Link>
        <ChevronRight className="h-3 w-3 text-zinc-400" />
        <span className="text-zinc-800 font-extrabold">{product.brand?.name}</span>
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-start">
        
        {/* Left Column: Media Showcase (Shopify/Amazon style layout) */}
        <div className="lg:col-span-7 flex flex-col gap-4">
          <div 
            onClick={() => openLightbox(activeImageIndex)}
            className="relative aspect-[4/5] bg-white rounded-2xl overflow-hidden border border-zinc-150/80 shadow-xs group cursor-zoom-in"
          >
            <Image
              src={mainImage}
              alt={product.title}
              fill
              priority
              sizes="(max-width: 768px) 100vw, 50vw"
              className="object-cover transition-transform duration-700 group-hover:scale-105"
            />
            <div className="absolute top-4 left-4 bg-white/90 backdrop-blur-md px-3.5 py-1.5 rounded-full shadow-sm text-[10px] font-black text-zinc-900 tracking-widest uppercase flex items-center gap-1.5">
              <Sparkles className="h-3 w-3 text-[#f51c50] fill-[#f51c50]" /> Verified Authentic
            </div>
            <div className="absolute bottom-4 right-4 bg-black/60 hover:bg-black/80 text-white p-2.5 rounded-full shadow-md transition-colors select-none">
              <Maximize2 className="h-4 w-4" />
            </div>
          </div>
          
          {/* Thumbnails list */}
          {product.media.length > 0 && (
            <div className="flex gap-3 overflow-x-auto py-1 select-none scrollbar-none justify-start">
              {product.media.map((med, idx) => (
                <button
                  key={idx}
                  onClick={() => setActiveImageIndex(idx)}
                  className={`relative w-20 aspect-[4/5] rounded-xl overflow-hidden bg-white border transition-all ${
                    activeImageIndex === idx 
                      ? "border-[#f51c50] ring-3 ring-[#f51c50]/15 scale-95 shadow-sm" 
                      : "border-zinc-200 hover:border-zinc-400 hover:scale-98"
                  }`}
                >
                  <Image
                    src={med.path}
                    alt={`Thumbnail ${idx + 1}`}
                    fill
                    sizes="80px"
                    className="object-cover"
                  />
                </button>
              ))}
            </div>
          )}
        </div>

        {/* Right Column: Checkout & Selection Details */}
        <div className="lg:col-span-5 flex flex-col text-left">
          
          {/* Header Info */}
          <div className="pb-4">
            <span className="text-[10px] font-black tracking-widest uppercase text-[#f51c50] bg-[#f51c50]/5 px-2.5 py-1 rounded-full w-fit">
              {product.brand?.name || "AURA BRAND"}
            </span>
            <h1 className="text-2xl sm:text-3xl font-black text-zinc-900 tracking-tight leading-tight mt-3">
              {product.title}
            </h1>
            
            {/* Rating summary badge */}
            <div className="flex items-center gap-2.5 mt-3 select-none">
              <div className="flex items-center gap-1 bg-emerald-500/10 text-emerald-700 px-3 py-1 rounded-full text-[11px] font-black">
                <span>4.2</span>
                <Star className="h-3.5 w-3.5 fill-emerald-600 text-emerald-600" />
              </div>
              <span className="text-zinc-300 font-bold">•</span>
              <span className="text-xs text-zinc-500 font-extrabold hover:underline cursor-pointer flex items-center gap-1">
                <MessageSquare className="h-3.5 w-3.5" /> {reviews.length} Ratings & Reviews
              </span>
            </div>
          </div>

          {/* Pricing Row card */}
          <div className="p-5 bg-white rounded-2xl border border-zinc-150/80 shadow-xs flex flex-col gap-1.5 mt-2">
            <span className="text-[9px] font-black text-zinc-450 uppercase tracking-widest">Premium Retail Price</span>
            <div className="flex items-baseline gap-3">
              <span className="text-3xl font-black text-zinc-950 tracking-tight">Rs. {salePrice || displayPrice}</span>
              {salePrice && (
                <>
                  <span className="text-base text-zinc-400 line-through">Rs. {displayPrice}</span>
                  <span className="bg-[#f51c50]/10 text-[#f51c50] text-[10px] px-2.5 py-1 rounded-full font-black uppercase tracking-wider">
                    {Math.round(((displayPrice - salePrice) / displayPrice) * 100)}% OFF
                  </span>
                </>
              )}
            </div>
            <span className="text-[9.5px] font-bold text-emerald-600 uppercase tracking-widest flex items-center gap-1.5 mt-1 select-none">
              <BadgePercent className="h-4 w-4" /> Inclusive of all taxes & free shipping
            </span>
          </div>

          {/* Colors Selection */}
          {uniqueColors.length > 0 && (
            <div className="mt-6 select-none">
              <h3 className="text-[10px] font-black uppercase tracking-widest text-zinc-400 mb-3">Select Color</h3>
              <div className="flex flex-wrap gap-2.5">
                {uniqueColors.map((color) => {
                  const isSelected = selectedColor === color;
                  return (
                    <button
                      key={color}
                      onClick={() => setSelectedColor(color === selectedColor ? null : color)}
                      className={`px-4 py-2.5 border text-xs font-black transition-all rounded-xl uppercase tracking-wider shadow-2xs ${
                        isSelected
                          ? "border-zinc-900 bg-zinc-900 text-white hover:bg-zinc-800"
                          : "border-zinc-200 bg-white hover:border-zinc-400 text-zinc-850"
                      }`}
                    >
                      {color}
                    </button>
                  );
                })}
              </div>
            </div>
          )}

          {/* Sizes Selection */}
          {uniqueSizes.length > 0 && (
            <div className="mt-6 select-none">
              <div className="flex justify-between items-center mb-3">
                <h3 className="text-[10px] font-black uppercase tracking-widest text-zinc-450">Select Size</h3>
                <button className="text-[10px] font-black text-[#f51c50] uppercase tracking-wider hover:underline">Size Chart &gt;</button>
              </div>
              <div className="flex flex-wrap gap-3">
                {uniqueSizes.map((size) => {
                  const isSelected = selectedSize === size;
                  return (
                    <button
                      key={size}
                      onClick={() => setSelectedSize(size === selectedSize ? null : size)}
                      className={`w-12 h-12 rounded-xl border text-xs font-black transition-all flex items-center justify-center shadow-2xs ${
                        isSelected
                          ? "border-[#f51c50] bg-[#f51c50] text-white hover:bg-[#f51c50]/90"
                          : "border-zinc-200 bg-white hover:border-zinc-400 text-zinc-800"
                      }`}
                    >
                      {size}
                    </button>
                  );
                })}
              </div>
            </div>
          )}

          {/* Action Triggers */}
          <div className="mt-8 flex flex-col sm:flex-row gap-4 select-none">
            <Button 
              onClick={handleAddToBag}
              className="flex-1 bg-[#f51c50] hover:bg-[#f51c50]/95 text-white font-black text-[11px] uppercase tracking-widest py-6.5 rounded-xl shadow-md gap-2 transition-all hover:scale-101 duration-200"
            >
              <ShoppingBag className="h-4 w-4" /> Add to Shopping Bag
            </Button>
            <Button 
              onClick={handleToggleWishlist}
              variant="outline" 
              className={`sm:w-1/3 border-zinc-300 bg-white font-black text-[11px] uppercase tracking-widest py-6.5 rounded-xl gap-2 hover:bg-zinc-50 transition-all ${
                isWishlisted ? "border-[#f51c50] text-[#f51c50] hover:bg-[#f51c50]/5" : "text-zinc-800"
              }`}
            >
              <Heart className={`h-4 w-4 ${isWishlisted ? "fill-[#f51c50] text-[#f51c50]" : "text-zinc-500"}`} /> 
              {isWishlisted ? "Wishlisted" : "Wishlist"}
            </Button>
          </div>

          {/* Value Propositions list */}
          <div className="mt-8 grid grid-cols-3 gap-3 border-y border-zinc-150/80 py-5 select-none text-center">
            <div className="flex flex-col items-center p-1">
              <ShieldCheck className="h-5 w-5 text-emerald-600 mb-1.5" />
              <span className="text-[10px] font-black text-zinc-900 uppercase tracking-wide">100% Genuine</span>
              <span className="text-[9px] text-zinc-400 font-bold mt-0.5">Stripe secured</span>
            </div>
            <div className="flex flex-col items-center p-1">
              <RotateCcw className="h-5 w-5 text-emerald-600 mb-1.5" />
              <span className="text-[10px] font-black text-zinc-900 uppercase tracking-wide">14 Days Exchange</span>
              <span className="text-[9px] text-zinc-400 font-bold mt-0.5">Hassle-free returns</span>
            </div>
            <div className="flex flex-col items-center p-1">
              <Truck className="h-5 w-5 text-emerald-600 mb-1.5" />
              <span className="text-[10px] font-black text-zinc-900 uppercase tracking-wide">Free Delivery</span>
              <span className="text-[9px] text-zinc-400 font-bold mt-0.5">On all items today</span>
            </div>
          </div>

          {/* Delivery Pincode Checker */}
          <div className="mt-6 border-b border-zinc-150/80 pb-6 select-none">
            <h3 className="text-[10px] font-black uppercase tracking-widest text-zinc-450 mb-3 flex items-center gap-1.5">
              <MapPin className="h-4 w-4 text-zinc-455" /> Delivery Options
            </h3>
            <form onSubmit={checkPincode} className="flex gap-2 max-w-sm">
              <Input
                type="text"
                placeholder="Enter 6-digit Pincode (e.g. 400001)"
                value={pincode}
                onChange={(e) => setPincode(e.target.value.replace(/\D/g, "").slice(0, 6))}
                className="text-xs rounded-xl py-5 border-zinc-200 focus-visible:ring-[#f51c50] bg-white shadow-2xs placeholder:text-zinc-400"
              />
              <Button type="submit" variant="ghost" className="text-[#f51c50] font-black text-xs uppercase hover:bg-[#f51c50]/5 tracking-wider">
                Check
              </Button>
            </form>
            {pincodeStatus && (
              <p className="text-[10px] font-bold text-emerald-600 mt-2.5 flex items-center gap-1">
                <Truck className="h-3.5 w-3.5" /> {pincodeStatus}
              </p>
            )}
          </div>

          {/* Tabbed Info Showcase (Shopify/Amazon style) */}
          <div className="mt-8 border border-zinc-200/80 rounded-2xl overflow-hidden bg-white shadow-3xs">
            <div className="flex border-b border-zinc-200 bg-zinc-50/50">
              {["description", "specifications", "wash"].map((tab) => (
                <button
                  key={tab}
                  onClick={() => setActiveTab(tab as any)}
                  className={`flex-1 py-3.5 text-center text-[10px] font-black uppercase tracking-widest transition-all border-b-2 ${
                    activeTab === tab
                      ? "border-[#f51c50] text-[#f51c50] bg-white font-black"
                      : "border-transparent text-zinc-450 hover:text-zinc-800"
                  }`}
                >
                  {tab}
                </button>
              ))}
            </div>
            <div className="p-5 text-xs text-zinc-650 leading-relaxed text-left">
              {activeTab === "description" && (
                <p className="whitespace-pre-line">{product.description}</p>
              )}
              
              {activeTab === "specifications" && (
                <div className="grid grid-cols-2 gap-4">
                  {(() => {
                    const specs = Array.isArray(product.specifications) 
                      ? product.specifications 
                      : (typeof product.specifications === "object" && product.specifications !== null) 
                        ? Object.entries(product.specifications).map(([name, value]) => ({ name, value: String(value) }))
                        : [];
                    return specs.length > 0 ? specs.map((spec: any, idx) => (
                      <div key={idx} className="border-b border-zinc-100 pb-2">
                        <span className="text-[10px] text-zinc-450 font-extrabold uppercase tracking-wider block">{spec.name}</span>
                        <span className="text-zinc-850 font-bold mt-0.5 block">{spec.value}</span>
                      </div>
                    )) : <p className="text-zinc-400 italic">No technical specs available.</p>;
                  })()}
                </div>
              )}
              
              {activeTab === "wash" && (
                <div className="space-y-3">
                  <div>
                    <span className="text-[10px] text-zinc-450 font-extrabold uppercase tracking-wider block">Care Instruction</span>
                    <span className="text-zinc-800 font-bold mt-0.5 block">{product.wash_care || "Dry clean only. Iron inside out."}</span>
                  </div>
                  <div className="pt-2">
                    <span className="text-[10px] text-zinc-450 font-extrabold uppercase tracking-wider block">Country of Origin</span>
                    <span className="text-zinc-800 font-bold mt-0.5 block">{product.origin_country || "Pakistan"}</span>
                  </div>
                </div>
              )}
            </div>
          </div>

        </div>
      </div>

      {/* Dynamic Review Engine Segment */}
      <section className="mt-20 border-t border-zinc-200/80 pt-12 text-left">
        <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
          <h2 className="font-heading text-lg font-black uppercase tracking-widest text-zinc-950">
            Customer Reviews ({reviews.length})
          </h2>
          <div className="flex items-center gap-1.5 bg-emerald-500/10 text-emerald-700 px-3.5 py-1.5 rounded-full text-xs font-black select-none">
            <span>4.2 / 5.0 Rating</span>
            <Star className="h-3.5 w-3.5 fill-emerald-600 text-emerald-600" />
          </div>
        </div>

        {/* Post Review Form */}
        <div className="bg-white border border-zinc-150/80 p-6 rounded-2xl shadow-xs mb-8">
          <h3 className="text-xs font-black uppercase tracking-widest text-zinc-850 mb-4">Post your Product Review</h3>
          <form onSubmit={handlePostReview} className="space-y-4">
            <div className="flex items-center gap-3">
              <span className="text-xs font-bold text-zinc-500">Rating:</span>
              <select
                value={rating}
                onChange={(e) => setRating(Number(e.target.value))}
                className="text-xs font-bold border border-zinc-200 rounded-lg px-3 py-1.5 bg-zinc-50/50 cursor-pointer"
              >
                {[5, 4, 3, 2, 1].map((val) => (
                  <option key={val} value={val}>{val} Stars</option>
                ))}
              </select>
            </div>
            <div>
              <Input
                type="text"
                placeholder="Review Title (e.g. Excellent Stitching Quality)"
                value={reviewTitle}
                onChange={(e) => setReviewTitle(e.target.value)}
                className="text-xs border-zinc-200 focus-visible:ring-[#f51c50] py-5 rounded-xl placeholder:text-zinc-400 bg-zinc-50/10"
              />
            </div>
            <div>
              <textarea
                placeholder="Write your detailed review here. Mention fabric quality, fitting specifications and print details..."
                value={reviewComment}
                onChange={(e) => setReviewComment(e.target.value)}
                className="w-full text-xs border border-zinc-200 rounded-xl p-4 focus-visible:ring-[#f51c50] outline-none bg-zinc-50/10 min-h-[100px] placeholder:text-zinc-400"
              />
            </div>
            <Button type="submit" className="bg-[#f51c50] hover:bg-[#f51c50]/95 text-white font-black text-xs uppercase px-7 py-2.5 rounded-xl shadow-xs transition-transform duration-100 active:scale-98">
              Submit Review
            </Button>
          </form>
        </div>

        {/* Review list */}
        <div className="space-y-4">
          {reviews.length > 0 ? (
            reviews.map((rev) => (
              <div key={rev.id} className="bg-white border border-zinc-150/80 p-5.5 rounded-2xl shadow-xs transition-all hover:border-zinc-250">
                <div className="flex items-center gap-2.5">
                  <span className="bg-emerald-600 text-white text-[9px] font-black px-2 py-0.5 rounded-full flex items-center gap-0.5 select-none">
                    {rev.rating} <Star className="h-2 w-2 fill-white text-white" />
                  </span>
                  <span className="text-xs font-black text-zinc-950">{rev.title}</span>
                </div>
                <p className="text-xs text-zinc-650 mt-2.5 leading-relaxed">{rev.comment}</p>
                <div className="flex items-center gap-3 mt-3.5 text-[9.5px] text-zinc-400 font-bold select-none">
                  <span>By {rev.user?.name || "Guest reviewer"}</span>
                  {rev.is_verified && (
                    <span className="text-emerald-600 uppercase italic font-extrabold">✔ Verified Purchase</span>
                  )}
                  <span className="text-zinc-200">|</span>
                  <span>{rev.helpful_votes} people found helpful</span>
                </div>
              </div>
            ))
          ) : (
            <div className="text-center py-8 text-xs text-zinc-400 italic">No reviews yet. Be the first to review!</div>
          )}
        </div>
      </section>

      {/* Similar Products */}
      {similar.length > 0 && (
        <section className="mt-20 border-t border-zinc-200/80 pt-12 text-left">
          <h2 className="font-heading text-lg font-black uppercase tracking-widest text-zinc-950 mb-8">
            Similar Products You May Like
          </h2>
          <div className="grid grid-cols-2 md:grid-cols-5 gap-6">
            {similar.map((item) => {
              const simImg = item.media?.[0]?.path || "https://images.unsplash.com/photo-1595950653106-6c9ebd614d3a?q=80&w=300&auto=format&fit=crop";
              const simPrice = item.variants?.[0]?.price || item.price;
              const simSalePrice = item.variants?.[0]?.sale_price;

              return (
                <Link 
                  key={item.id} 
                  href={getRelativePath(`/product/?id=${item.slug}`)} 
                  className="group flex flex-col cursor-pointer bg-white border border-zinc-150 rounded-2xl overflow-hidden shadow-2xs hover:shadow-md transition-all duration-300 hover:scale-101"
                >
                  <div className="relative aspect-[3/4] w-full bg-zinc-50 overflow-hidden">
                    <Image 
                      src={simImg} 
                      alt={item.title} 
                      fill 
                      sizes="(max-width: 768px) 50vw, 15vw" 
                      className="object-cover transition-transform duration-500 group-hover:scale-104" 
                    />
                  </div>
                  <div className="p-3.5 flex flex-col flex-1">
                    <h3 className="text-[10px] font-black uppercase tracking-widest text-zinc-900 leading-tight">{item.brand?.name}</h3>
                    <p className="text-[10.5px] text-zinc-450 truncate mt-1 leading-tight">{item.title}</p>
                    <div className="flex items-center gap-1.5 mt-auto pt-3 text-[11px] font-black">
                      <span className="text-zinc-950">Rs. {simSalePrice || simPrice}</span>
                      {simSalePrice && (
                        <span className="text-[#f51c50] text-[9.5px]">
                          ({Math.round(((simPrice - simSalePrice) / simPrice) * 100)}% OFF)
                        </span>
                      )}
                    </div>
                  </div>
                </Link>
              );
            })}
          </div>
        </section>
      )}

      {/* Lightbox Modal Popup */}
      {isLightboxOpen && (
        <div className="fixed inset-0 z-[100] flex items-center justify-center bg-black/90 backdrop-blur-md animate-fade-in select-none">
          {/* Close button */}
          <button 
            onClick={() => setIsLightboxOpen(false)}
            className="absolute top-6 right-6 text-white/75 hover:text-white bg-white/10 hover:bg-white/20 p-3 rounded-full transition-all focus:outline-none text-sm font-bold z-[110]"
          >
            ✕ Close
          </button>

          {/* Left Navigation Arrow */}
          <button 
            onClick={() => setLightboxImageIndex((prev) => (prev - 1 + product.media.length) % product.media.length)}
            className="absolute left-6 text-white/75 hover:text-white bg-white/10 hover:bg-white/20 p-4 rounded-xl transition-all focus:outline-none text-lg font-black z-[110]"
          >
            &lt;
          </button>

          {/* Large Image display */}
          <div className="relative w-[90vw] h-[75vh] max-w-4xl max-h-[80vh] flex flex-col items-center justify-center z-[105]">
            <div className="relative w-full h-full">
              <Image
                src={product.media[lightboxImageIndex]?.path || "/placeholder.jpg"}
                alt={`Lightbox image ${lightboxImageIndex + 1}`}
                fill
                priority
                className="object-contain"
              />
            </div>
            
            {/* Image pagination index counter */}
            <div className="text-white/60 text-xs font-black uppercase mt-4 tracking-widest bg-white/5 px-4 py-2 rounded-full">
              Image {lightboxImageIndex + 1} of {product.media.length}
            </div>
          </div>

          {/* Right Navigation Arrow */}
          <button 
            onClick={() => setLightboxImageIndex((prev) => (prev + 1) % product.media.length)}
            className="absolute right-6 text-white/75 hover:text-white bg-white/10 hover:bg-white/20 p-4 rounded-xl transition-all focus:outline-none text-lg font-black z-[110]"
          >
            &gt;
          </button>
        </div>
      )}

    </div>
  );
}
