"use client";
import * as React from "react";
import Link from "next/link";
import Image from "next/image";
import { getRelativePath } from "@/lib/utils";

// ─── Types ────────────────────────────────────────────────────────────────────
interface Section {
  id: number;
  key: string;
  title: string;
  subtitle: string | null;
  description: string | null;
  background: { type: string; color: string | null };
  padding: string | null;
  width: string;
  button_text: string | null;
  button_url: string | null;
  layout_variation: string;
  show_on_mobile: boolean;
  show_on_desktop: boolean;
  settings: any;
}
interface Product {
  id: number;
  title: string;
  slug: string;
  media?: { path: string }[];
  variants?: { price: number; sale_price: number | null }[];
  label?: { name: string; bg_color: string; text_color: string };
}
interface SlideImg { url?: string }
interface Slide {
  design: string; active: boolean; label?: string;
  height?: string; width?: string; bg?: string;
  title?: string; subtitle?: string; price?: string;
  logoText?: string; logoColor?: string;
  btnLabel?: string; btnUrl?: string;
  images?: (SlideImg | string)[];
}

// ─── Design Presets for hero slider ─────────────────────────────────────────
const PRESETS: Record<string, { bg: string; logoColor: string; dark?: boolean }> = {
  classic_gradient: { bg: "from-[#fae04b] via-[#fbd83b] to-[#fae66d]",  logoColor: "text-zinc-950" },
  rose_pink:        { bg: "from-[#ffd6e7] via-[#ffafd1] to-[#ff85bb]",  logoColor: "text-[#c2185b]" },
  dark_night:       { bg: "from-[#1a1a2e] via-[#16213e]  to-[#0f3460]", logoColor: "text-[#e94560]", dark: true },
  mint_sports:      { bg: "from-[#d4fc79] via-[#96e6a1]  to-[#43e97b]", logoColor: "text-[#1b5e20]" },
  custom:           { bg: "from-zinc-100 to-zinc-200",                   logoColor: "text-zinc-400" },
};
function imgSrc(img: SlideImg | string): string {
  return typeof img === "string" ? img : (img?.url ?? "");
}
function getPrice(p: Product): number {
  return p.variants?.[0]?.sale_price ?? p.variants?.[0]?.price ?? 0;
}
function getOrigPrice(p: Product): number | null {
  const v = p.variants?.[0];
  return v?.sale_price ? v.price : null;
}
function getImg(p: Product): string {
  return p.media?.[0]?.path ?? "https://placehold.co/300x400/f1f5f9/94a3b8?text=No+Image";
}

// ─── Product Card ────────────────────────────────────────────────────────────
function ProductCard({ p }: { p: Product }) {
  const price     = getPrice(p);
  const origPrice = getOrigPrice(p);
  const img       = getImg(p);
  const discount  = origPrice ? Math.round(((origPrice - price) / origPrice) * 100) : 0;
  return (
    <Link href={getRelativePath(`/product/${p.slug}`)}
      className="group bg-white border border-zinc-100 hover:shadow-md transition-shadow duration-300 flex flex-col overflow-hidden">
      <div className="relative aspect-[3/4] bg-zinc-50 overflow-hidden">
        <Image src={img} alt={p.title} fill sizes="(max-width:768px) 50vw,25vw"
          className="object-cover transition-transform duration-500 group-hover:scale-105" />
        {p.label && (
          <span className="absolute top-2 left-2 text-[9px] font-black uppercase px-2 py-0.5 rounded-sm"
            style={{ background: p.label.bg_color, color: p.label.text_color }}>
            {p.label.name}
          </span>
        )}
        {discount >= 5 && (
          <span className="absolute top-2 right-2 text-[9px] font-black bg-[#ff3f6c] text-white px-2 py-0.5 rounded-sm">
            -{discount}%
          </span>
        )}
      </div>
      <div className="p-3 flex flex-col gap-1">
        <h3 className="text-xs font-bold text-zinc-800 line-clamp-2 leading-snug">{p.title}</h3>
        <div className="flex items-center gap-2 mt-1">
          <span className="text-sm font-black text-zinc-900">Rs {price.toLocaleString()}</span>
          {origPrice && <span className="text-xs text-zinc-400 line-through">Rs {origPrice.toLocaleString()}</span>}
        </div>
      </div>
    </Link>
  );
}

// ─── Section Heading ─────────────────────────────────────────────────────────
function SectionHead({ title, subtitle, btnText, btnUrl, dark }: {
  title: string; subtitle?: string | null;
  btnText?: string | null; btnUrl?: string | null; dark?: boolean;
}) {
  return (
    <div className="flex items-end justify-between mb-7">
      <div>
        <h2 className={`font-heading text-lg sm:text-xl font-black uppercase tracking-widest border-b-2 border-[#f51c50] pb-1.5 inline-block ${dark ? "text-white" : "text-zinc-900"}`}>
          {title}
        </h2>
        {subtitle && <p className={`text-sm mt-1.5 font-medium ${dark ? "text-zinc-400" : "text-zinc-500"}`}>{subtitle}</p>}
      </div>
      {btnText && btnUrl && (
        <Link href={getRelativePath(btnUrl)}
          className={`text-[11px] font-black uppercase tracking-wider border px-4 py-1.5 hover:bg-[#ff3f6c] hover:text-white hover:border-[#ff3f6c] transition-colors ${dark ? "border-zinc-600 text-zinc-300" : "border-zinc-300 text-zinc-700"}`}>
          {btnText}
        </Link>
      )}
    </div>
  );
}

// ─── Countdown Timer ─────────────────────────────────────────────────────────
function Countdown({ endTime }: { endTime: string }) {
  const calc = () => {
    const diff = Math.max(0, new Date(endTime).getTime() - Date.now());
    return {
      h: Math.floor(diff / 3600000),
      m: Math.floor((diff % 3600000) / 60000),
      s: Math.floor((diff % 60000) / 1000),
    };
  };
  const [t, setT] = React.useState(calc);
  React.useEffect(() => { const i = setInterval(() => setT(calc()), 1000); return () => clearInterval(i); });
  const pad = (n: number) => String(n).padStart(2, "0");
  return (
    <div className="flex items-center gap-2">
      {[["h", t.h], ["m", t.m], ["s", t.s]].map(([label, val]) => (
        <div key={String(label)} className="flex flex-col items-center">
          <span className="bg-[#ff3f6c] text-white font-black text-lg sm:text-2xl w-12 sm:w-14 h-10 sm:h-12 flex items-center justify-center rounded">{pad(Number(val))}</span>
          <span className="text-[9px] font-bold text-zinc-500 uppercase mt-0.5">{label}</span>
        </div>
      ))}
    </div>
  );
}

// ─── Main Page ───────────────────────────────────────────────────────────────
export default function Home() {
  const API = process.env.NEXT_PUBLIC_API_URL ?? "http://localhost:8000";

  const [sections, setSections]           = React.useState<Section[]>([]);
  const [currentSlide, setCurrentSlide]   = React.useState(0);
  const [cardSlide, setCardSlide]         = React.useState(0);
  const [brandIdx, setBrandIdx]           = React.useState(0);
  const [testimonialIdx, setTestimonialIdx] = React.useState(0);
  const [dragStart, setDragStart]         = React.useState<number | null>(null);
  const [dragOffset, setDragOffset]       = React.useState(0);
  const [products, setProducts]           = React.useState<Record<string, Product[]>>({});
  const [testimonials, setTestimonials]   = React.useState<any[]>([]);

  // ── Fetch sections ───────────────────────────────────────────────────────
  React.useEffect(() => {
    fetch(`${API}/api/v1/homepage`)
      .then(r => r.ok ? r.json() : null)
      .then(d => { if (d?.sections) setSections(d.sections); })
      .catch(() => {});
  }, [API]);

  // ── Fetch products for product-grid sections + testimonials ──────────────
  React.useEffect(() => {
    if (!sections.length) return;
    const productSections = ["featured_products","trending_products","best_sellers","new_arrivals","flash_sale"];
    const fetched: Record<string, Product[]> = {};

    Promise.all(
      sections
        .filter(s => productSections.includes(s.key) && s.settings?.api_url)
        .map(s =>
          fetch(`${API}${s.settings.api_url}`)
            .then(r => r.ok ? r.json() : null)
            .then(d => { if (d?.products) fetched[s.key] = d.products.slice(0, s.settings?.limit ?? 8); })
            .catch(() => {})
        )
    ).then(() => setProducts(fetched));

    // testimonials
    const tSec = sections.find(s => s.key === "testimonials");
    if (tSec?.settings?.api_url) {
      fetch(`${API}${tSec.settings.api_url}`)
        .then(r => r.ok ? r.json() : null)
        .then(d => { if (Array.isArray(d)) setTestimonials(d); })
        .catch(() => {});
    }
  }, [sections, API]);

  // ── Hero slider autoplay ─────────────────────────────────────────────────
  const heroSec       = sections.find(s => s.key === "hero_slider");
  const allSlides     = (heroSec?.settings?.slides ?? []) as Slide[];
  const activeSlides  = allSlides.filter(s => s.active !== false);
  const slidesCount   = activeSlides.length;
  const autoplay      = heroSec?.settings?.autoplay !== false;
  const autoplayDelay = Number(heroSec?.settings?.autoplay_delay ?? 4500);

  React.useEffect(() => {
    if (!autoplay || slidesCount <= 1) return;
    const t = setInterval(() => setCurrentSlide(p => (p + 1) % slidesCount), autoplayDelay);
    return () => clearInterval(t);
  }, [slidesCount, autoplay, autoplayDelay]);

  // ── Bank offers rotation ─────────────────────────────────────────────────
  const bankSec      = sections.find(s => s.key === "bank_offers");
  const offersCount  = bankSec?.settings?.offers?.length ?? 0;
  React.useEffect(() => {
    if (offersCount <= 1) return;
    const t = setInterval(() => setCardSlide(p => (p + 1) % offersCount), 3000);
    return () => clearInterval(t);
  }, [offersCount]);

  // ── Brand carousel rotation ──────────────────────────────────────────────
  const brandSec    = sections.find(s => s.key === "brand_carousel");
  const brandsCount = brandSec?.settings?.brands?.length ?? 0;
  React.useEffect(() => {
    if (brandsCount <= 4) return;
    const t = setInterval(() => setBrandIdx(p => (p + 1) % brandsCount), 2500);
    return () => clearInterval(t);
  }, [brandsCount]);

  // ── Testimonial rotation ─────────────────────────────────────────────────
  React.useEffect(() => {
    if (testimonials.length <= 1) return;
    const t = setInterval(() => setTestimonialIdx(p => (p + 1) % testimonials.length), 4000);
    return () => clearInterval(t);
  }, [testimonials.length]);

  // ── Drag/swipe ───────────────────────────────────────────────────────────
  const onDragStart = (x: number) => setDragStart(x);
  const onDragMove  = (x: number) => { if (dragStart !== null) setDragOffset(x - dragStart); };
  const onDragEnd   = () => {
    if (dragStart === null) return;
    if (dragOffset > 50)  setCurrentSlide(p => (p - 1 + slidesCount) % slidesCount);
    if (dragOffset < -50) setCurrentSlide(p => (p + 1) % slidesCount);
    setDragStart(null); setDragOffset(0);
  };

  const sliderH = heroSec?.settings?.slider_height ?? "420px";
  const sliderW = heroSec?.settings?.slider_width  ?? "100%";

  // ── Render ───────────────────────────────────────────────────────────────
  return (
    <div className="flex flex-col min-h-screen bg-zinc-50/40">

      {/* Announcement Bar */}
      <div className="w-full bg-[#f51c50] py-2.5 px-4 text-center text-white font-black tracking-widest text-[11px] sm:text-xs flex items-center justify-center gap-4 select-none">
        <span>FLAT Rs. 500 OFF + FREE SHIPPING ON ORDERS ABOVE Rs. 2000</span>
        <Link href={getRelativePath("/register")}>
          <span className="bg-white text-[#f51c50] text-[10px] font-black uppercase px-3 py-1 rounded-sm cursor-pointer hover:opacity-90 transition-opacity">
            USE CODE: AURA500
          </span>
        </Link>
      </div>

      {/* Coupon Strip */}
      <div className="mx-auto max-w-7xl w-full px-4 sm:px-6 lg:px-8 pt-5">
        <div className="relative bg-gradient-to-r from-[#ffebe0] to-[#ffdcd0] border border-[#ffcdb3] rounded-xl py-3.5 px-6 sm:px-10 flex flex-col md:flex-row items-center justify-between gap-3 overflow-hidden shadow-sm">
          <div className="absolute -left-3.5 top-1/2 -translate-y-1/2 w-7 h-7 bg-zinc-50/80 rounded-full border-r border-[#ffcdb3]" />
          <div className="absolute -right-3.5 top-1/2 -translate-y-1/2 w-7 h-7 bg-zinc-50/80 rounded-full border-l border-[#ffcdb3]" />
          <div className="text-center md:text-left">
            <div className="text-2xl font-black text-[#ff5d24]">Get 25% Off</div>
            <div className="text-xs font-extrabold text-zinc-600 tracking-wide">Up To Rs 200 Off · First Order</div>
          </div>
          <div className="flex flex-col items-center gap-1">
            <div className="bg-white border border-[#ffbca0] rounded-lg px-5 py-2 flex items-center gap-3 shadow-sm">
              <div className="text-[9px] font-black uppercase text-zinc-400 leading-tight">COUPON<br/>CODE</div>
              <span className="text-lg font-black text-zinc-800 tracking-wider">AURA25</span>
            </div>
            <span className="text-[10px] text-zinc-500 font-bold">On Your First Order · T&amp;C Apply</span>
          </div>
        </div>
      </div>

      {/* ── Section Loop ─────────────────────────────────────────────────── */}
      {sections.map(section => {
        const vis = [
          !section.show_on_mobile  ? "hidden md:block" : "",
          !section.show_on_desktop ? "md:hidden" : "",
        ].filter(Boolean).join(" ");
        const wrap = section.width === "full"
          ? `w-full ${section.padding ?? "py-10"} ${vis}`
          : `mx-auto max-w-7xl w-full px-4 sm:px-6 lg:px-8 ${section.padding ?? "py-10"} ${vis}`;

        // ══════════════════════════════════════════════════════════════════
        // 1. HERO SLIDER
        // ══════════════════════════════════════════════════════════════════
        if (section.key === "hero_slider") {
          if (!activeSlides.length) return null;
          return (
            <div key={section.id} className={`w-full px-4 sm:px-6 lg:px-8 ${section.padding ?? "py-6"} ${vis}`}>
              <div
                style={{ height: sliderH, width: sliderW, maxWidth: "100%" }}
                className="relative mx-auto rounded-2xl overflow-hidden shadow-md cursor-grab active:cursor-grabbing select-none"
                onMouseDown={e => onDragStart(e.clientX)} onMouseMove={e => onDragMove(e.clientX)}
                onMouseUp={onDragEnd} onMouseLeave={onDragEnd}
                onTouchStart={e => onDragStart(e.touches[0].clientX)}
                onTouchMove={e => onDragMove(e.touches[0].clientX)} onTouchEnd={onDragEnd}
              >
                {activeSlides.map((slide, i) => {
                  const preset   = PRESETS[slide.design ?? "custom"] ?? PRESETS.custom;
                  const bg       = slide.bg ?? preset.bg;
                  const isDark   = preset.dark ?? false;
                  const img0     = slide.images?.[0] ? imgSrc(slide.images[0]) : "";
                  const img1     = slide.images?.[1] ? imgSrc(slide.images[1]) : "";
                  return (
                    <div key={i} className={`absolute inset-0 bg-gradient-to-r ${bg} flex items-center justify-between px-6 sm:px-16 md:px-20 transition-opacity duration-700 ${currentSlide === i ? "opacity-100 z-10" : "opacity-0 z-0"}`}>
                      {/* Left: Model images */}
                      <div className="relative h-full w-[38%] sm:w-[44%] flex items-end pointer-events-none">
                        {img0 && (
                          <div className="absolute left-0 bottom-0 w-[65%] h-[78%] rounded-t-xl overflow-hidden border-2 border-white/20 shadow-md">
                            <Image src={img0} alt="model" fill priority className="object-cover object-top" />
                          </div>
                        )}
                        {img1 && (
                          <div className="absolute left-[28%] bottom-0 w-[72%] h-[90%] rounded-t-xl overflow-hidden border-2 border-white/40 shadow-xl">
                            <Image src={img1} alt="model" fill priority className="object-cover object-top" />
                          </div>
                        )}
                      </div>
                      {/* Centre: watermark */}
                      <div className="absolute inset-0 flex items-center justify-center pointer-events-none z-10">
                        <span className={`text-[14vw] sm:text-[10vw] font-black italic tracking-tighter uppercase opacity-15 ${slide.logoColor ?? preset.logoColor}`}>
                          {slide.logoText ?? "AURA"}
                        </span>
                      </div>
                      {/* Right: content */}
                      <div className="flex flex-col items-end text-right z-20 max-w-[48%]">
                        <h2 className={`font-black text-lg sm:text-3xl md:text-4xl uppercase tracking-wider leading-none ${isDark ? "text-white" : "text-zinc-900"}`}>
                          {slide.title ?? "New Collection"}
                        </h2>
                        <div className="flex items-baseline gap-2 mt-2 sm:mt-3">
                          <span className={`font-bold text-xs sm:text-sm uppercase ${isDark ? "text-zinc-400" : "text-zinc-600"}`}>{slide.subtitle}</span>
                          <span className={`font-black text-xl sm:text-4xl ${isDark ? "text-white" : "text-zinc-950"}`}>{slide.price}</span>
                        </div>
                        <Link href={getRelativePath(slide.btnUrl ?? "/catalog/")} className="mt-4 z-30">
                          <span className={`inline-block font-black text-[10px] sm:text-xs uppercase tracking-widest px-5 py-2.5 shadow rounded-sm cursor-pointer transition-colors ${isDark ? "bg-[#e94560] text-white hover:bg-[#c0392b]" : "bg-white text-zinc-950 hover:bg-zinc-100"}`}>
                            {slide.btnLabel ?? "SHOP NOW"}
                          </span>
                        </Link>
                      </div>
                    </div>
                  );
                })}
                {/* Arrows */}
                {slidesCount > 1 && (<>
                  <button onClick={() => setCurrentSlide(p => (p - 1 + slidesCount) % slidesCount)}
                    className="absolute left-3 top-1/2 -translate-y-1/2 w-8 h-8 rounded-full bg-white/70 hover:bg-white flex items-center justify-center text-zinc-800 font-bold shadow z-30 cursor-pointer">&#8592;</button>
                  <button onClick={() => setCurrentSlide(p => (p + 1) % slidesCount)}
                    className="absolute right-3 top-1/2 -translate-y-1/2 w-8 h-8 rounded-full bg-white/70 hover:bg-white flex items-center justify-center text-zinc-800 font-bold shadow z-30 cursor-pointer">&#8594;</button>
                </>)}
                {/* Dots */}
                <div className="absolute bottom-3 left-1/2 -translate-x-1/2 flex gap-1.5 z-30">
                  {activeSlides.map((_, idx) => (
                    <button key={idx} onClick={() => setCurrentSlide(idx)}
                      className={`h-2 rounded-full transition-all cursor-pointer ${currentSlide === idx ? "w-6 bg-[#ff3f6c]" : "w-2 bg-white/60 hover:bg-white"}`} />
                  ))}
                </div>
                {/* Design label */}
                {activeSlides[currentSlide]?.label && (
                  <span className="absolute bottom-3 right-3 z-30 hidden sm:block text-[9px] font-bold bg-black/25 text-white px-2 py-0.5 rounded-full backdrop-blur-sm">
                    {activeSlides[currentSlide].label}
                  </span>
                )}
              </div>
            </div>
          );
        }

        // ══════════════════════════════════════════════════════════════════
        // 2. BANK OFFERS
        // ══════════════════════════════════════════════════════════════════
        if (section.key === "bank_offers") {
          const offers = section.settings?.offers ?? [];
          if (!offers.length) return null;
          return (
            <div key={section.id} className={`mx-auto max-w-7xl w-full px-4 sm:px-6 lg:px-8 pb-5 ${vis}`}>
              <div className="flex gap-1.5 justify-center mb-2">
                {offers.map((_: any, i: number) => (
                  <span key={i} className={`h-1.5 rounded-full transition-all ${cardSlide === i ? "w-5 bg-zinc-700" : "w-1.5 bg-zinc-200"}`} />
                ))}
              </div>
              <div className="relative border border-[#d6c7ff] rounded-2xl bg-white h-20 sm:h-24 overflow-hidden shadow-sm">
                <div className="absolute right-[12%] top-0 bottom-0 border-l border-dashed border-zinc-200 z-10" />
                {offers.map((o: any, i: number) => (
                  <div key={i} className={`absolute inset-0 flex items-center px-4 sm:px-8 gap-4 transition-opacity duration-500 ${cardSlide === i ? "opacity-100 z-10" : "opacity-0 z-0"}`}>
                    <div className="w-[28%] shrink-0">
                      {o.type === "hdfc"         && <span className="text-blue-800 font-black text-xs tracking-widest">HDFC BANK</span>}
                      {o.type === "bob-kotak"    && <div className="flex gap-1 items-center"><span className="text-orange-600 font-extrabold text-[10px]">BOBCARD</span><span className="bg-red-600 text-white font-black text-[9px] px-1 py-0.5 rounded-sm">kotak</span></div>}
                      {o.type === "flipkart-sbi" && <span className="text-blue-700 font-black text-[10px]">Flipkart SBI</span>}
                      {o.type === "scb"          && <span className="text-emerald-600 font-black text-[10px]">Std. Chartered</span>}
                    </div>
                    <div className="flex-1 border-l border-zinc-100 pl-4">
                      <div className="font-black text-xs sm:text-sm text-zinc-900">{o.discount}</div>
                      <div className="text-[10px] text-zinc-400 font-semibold mt-0.5">{o.desc}</div>
                    </div>
                    <span className="text-[8px] font-black text-zinc-300 tracking-widest uppercase rotate-90 whitespace-nowrap w-[10%]">*T&amp;C Apply</span>
                  </div>
                ))}
              </div>
            </div>
          );
        }

        // ══════════════════════════════════════════════════════════════════
        // 3. CATEGORIES GRID
        // ══════════════════════════════════════════════════════════════════
        if (section.key === "categories") {
          const cats: any[] = section.settings?.categories ?? [];
          return (
            <div key={section.id} className={wrap}
              style={{ background: section.background?.color ?? undefined }}>
              <SectionHead title={section.title} subtitle={section.subtitle}
                btnText={section.button_text} btnUrl={section.button_url} />
              {!cats.length ? (
                <p className="text-center text-zinc-400 py-10 text-sm">No categories found. Run the seeder to populate data.</p>
              ) : (
                <div className="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-3 sm:gap-4">
                  {cats.map((cat: any, idx: number) => (
                    <Link key={idx}
                      href={getRelativePath(cat.url ?? `/catalog/?category=${encodeURIComponent((cat.title ?? "").toLowerCase())}`)}
                      className="group border-[3px] border-[#ffd5bd] p-1.5 bg-white hover:border-[#ff3f6c] hover:shadow-md transition-all duration-300 flex flex-col">
                      <div className="relative aspect-[3/4] w-full bg-zinc-100 overflow-hidden">
                        {cat.image && (
                          <Image src={cat.image} alt={cat.title ?? ""} fill
                            sizes="(max-width:768px) 50vw,20vw"
                            className="object-cover transition-transform duration-500 group-hover:scale-105" />
                        )}
                      </div>
                      <div className="py-2.5 text-center">
                        <div className="text-[12px] font-black text-zinc-800 uppercase tracking-wide">{cat.title}</div>
                        <div className="text-[13px] font-black text-[#ff3f6c] mt-0.5 uppercase">{cat.discount}</div>
                        <div className="text-[10px] font-bold text-zinc-400 mt-1 underline group-hover:text-[#ff3f6c] transition-colors">Shop Now</div>
                      </div>
                    </Link>
                  ))}
                </div>
              )}
            </div>
          );
        }

        // ══════════════════════════════════════════════════════════════════
        // 4. BUDGET BARGAINS
        // ══════════════════════════════════════════════════════════════════
        if (section.key === "budget_bargains") {
          const items: any[] = section.settings?.items ?? [];
          return (
            <div key={section.id} className={wrap}>
              <SectionHead title={section.title} subtitle={section.subtitle}
                btnText={section.button_text} btnUrl={section.button_url} />
              <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
                {items.map((item: any, i: number) => (
                  <Link key={i} href={getRelativePath(`/catalog/?tag=${encodeURIComponent((item.title ?? "").toLowerCase())}`)}
                    className="group relative aspect-[4/5] overflow-hidden rounded-xl shadow-sm hover:shadow-md transition-shadow">
                    <Image src={item.image} alt={item.title} fill sizes="(max-width:768px) 50vw,25vw"
                      className="object-cover transition-transform duration-500 group-hover:scale-105" />
                    <div className="absolute inset-0 bg-gradient-to-t from-black/85 via-black/20 to-transparent p-4 flex flex-col justify-end text-white">
                      <div className="font-black text-sm uppercase tracking-wide">{item.title}</div>
                      <div className="text-[10px] text-zinc-300 font-semibold mt-0.5">{item.tagline}</div>
                    </div>
                  </Link>
                ))}
              </div>
            </div>
          );
        }

        // ══════════════════════════════════════════════════════════════════
        // 5. PROMO BANNERS
        // ══════════════════════════════════════════════════════════════════
        if (section.key === "promo_banners") {
          const banners: any[] = section.settings?.banners ?? [];
          if (!banners.length) return null;
          return (
            <div key={section.id} className={wrap}>
              <SectionHead title={section.title} subtitle={section.subtitle} />
              <div className={`grid gap-4 ${banners.length === 1 ? "" : "md:grid-cols-2"}`}>
                {banners.map((b: any, i: number) => (
                  <Link key={i} href={getRelativePath(b.buttonUrl ?? "/catalog/")}
                    className="group relative h-56 sm:h-72 overflow-hidden rounded-xl shadow hover:shadow-lg transition-shadow">
                    <Image src={b.image} alt={b.title} fill sizes="(max-width:768px) 100vw,50vw"
                      className="object-cover transition-transform duration-700 group-hover:scale-105" />
                    <div className="absolute inset-0 bg-gradient-to-r from-black/60 via-black/25 to-transparent p-6 flex flex-col justify-end">
                      <h3 className="text-white font-black text-xl sm:text-2xl uppercase">{b.title}</h3>
                      {b.subtitle && <p className="text-zinc-300 text-sm mt-1">{b.subtitle}</p>}
                      {b.buttonText && (
                        <span className="mt-3 inline-block bg-white text-zinc-900 font-black text-[10px] uppercase tracking-widest px-4 py-2 rounded-sm w-fit">
                          {b.buttonText}
                        </span>
                      )}
                    </div>
                  </Link>
                ))}
              </div>
            </div>
          );
        }

        // ══════════════════════════════════════════════════════════════════
        // 6-9. PRODUCT GRID SECTIONS (featured / trending / best_sellers / new_arrivals)
        // ══════════════════════════════════════════════════════════════════
        const productSections = ["featured_products","trending_products","best_sellers","new_arrivals"];
        if (productSections.includes(section.key)) {
          const prods: Product[] = products[section.key] ?? [];
          const bgStyle = section.background?.color ? { background: section.background.color } : {};
          return (
            <div key={section.id} className={wrap} style={bgStyle}>
              <SectionHead title={section.title} subtitle={section.subtitle}
                btnText={section.button_text} btnUrl={section.button_url} />
              {!prods.length ? (
                <div className="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3 animate-pulse">
                  {Array.from({ length: 4 }).map((_, i) => (
                    <div key={i} className="bg-zinc-100 rounded aspect-[3/4]" />
                  ))}
                </div>
              ) : (
                <div className="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3 sm:gap-4">
                  {prods.map(p => <ProductCard key={p.id} p={p} />)}
                </div>
              )}
            </div>
          );
        }

        // ══════════════════════════════════════════════════════════════════
        // 10. FLASH SALE
        // ══════════════════════════════════════════════════════════════════
        if (section.key === "flash_sale") {
          const prods: Product[] = products["flash_sale"] ?? [];
          const endTime: string = section.settings?.end_time ?? new Date(Date.now() + 86400000).toISOString();
          return (
            <div key={section.id} className={wrap} style={{ background: section.background?.color ?? "#fff1f2" }}>
              <div className="flex flex-col sm:flex-row sm:items-center gap-4 mb-7">
                <div className="flex-1">
                  <h2 className="font-heading text-lg sm:text-xl font-black uppercase tracking-widest text-zinc-900 border-b-2 border-[#f51c50] pb-1.5 inline-block">
                    ⚡ {section.title}
                  </h2>
                  {section.subtitle && <p className="text-sm text-zinc-500 mt-1">{section.subtitle}</p>}
                </div>
                <div className="flex items-center gap-3">
                  <span className="text-xs font-bold text-zinc-500 uppercase tracking-wide">Ends In:</span>
                  <Countdown endTime={endTime} />
                </div>
                {section.button_text && section.button_url && (
                  <Link href={getRelativePath(section.button_url)}>
                    <span className="text-[11px] font-black uppercase tracking-wider border border-zinc-300 px-4 py-1.5 hover:bg-[#ff3f6c] hover:text-white hover:border-[#ff3f6c] transition-colors cursor-pointer">
                      {section.button_text}
                    </span>
                  </Link>
                )}
              </div>
              {!prods.length ? (
                <div className="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3 animate-pulse">
                  {Array.from({ length: 4 }).map((_, i) => <div key={i} className="bg-zinc-100 rounded aspect-[3/4]" />)}
                </div>
              ) : (
                <div className="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3 sm:gap-4">
                  {prods.map(p => <ProductCard key={p.id} p={p} />)}
                </div>
              )}
            </div>
          );
        }

        // ══════════════════════════════════════════════════════════════════
        // 11. BRAND CAROUSEL
        // ══════════════════════════════════════════════════════════════════
        if (section.key === "brand_carousel") {
          const brands: any[] = section.settings?.brands ?? [];
          if (!brands.length) return null;
          const visible = brands.slice(brandIdx, brandIdx + 5).concat(brands.slice(0, Math.max(0, brandIdx + 5 - brands.length)));
          return (
            <div key={section.id} className={wrap}>
              <SectionHead title={section.title} subtitle={section.subtitle} />
              <div className="border-y border-zinc-100 py-6">
                <div className="flex items-center justify-center gap-4 sm:gap-8 flex-wrap">
                  {brands.map((b: any, i: number) => (
                    <Link key={i} href={getRelativePath(b.url ?? "/catalog/")}
                      className="group opacity-60 hover:opacity-100 transition-opacity grayscale hover:grayscale-0">
                      <Image src={b.logo} alt={b.name} width={120} height={45}
                        className="object-contain h-8 sm:h-10 w-auto transition-transform group-hover:scale-105" />
                    </Link>
                  ))}
                </div>
              </div>
            </div>
          );
        }

        // ══════════════════════════════════════════════════════════════════
        // 12. LOOKBOOK
        // ══════════════════════════════════════════════════════════════════
        if (section.key === "lookbook") {
          const looks: any[] = section.settings?.looks ?? [];
          if (!looks.length) return null;
          return (
            <div key={section.id} className={`w-full ${section.padding ?? "py-14"} ${vis}`}
              style={{ background: section.background?.color ?? "#0f172a" }}>
              <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <SectionHead title={section.title} subtitle={section.subtitle}
                  btnText={section.button_text} btnUrl={section.button_url} dark />
                <div className="grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-4">
                  {looks.map((look: any, i: number) => (
                    <Link key={i} href={getRelativePath(look.url ?? "/catalog/")}
                      className="group relative aspect-[3/4] overflow-hidden rounded-xl">
                      <Image src={look.image} alt={look.title} fill sizes="(max-width:768px) 50vw,25vw"
                        className="object-cover transition-transform duration-700 group-hover:scale-105" />
                      <div className="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent p-4 flex flex-col justify-end">
                        <span className="text-[9px] font-black uppercase tracking-widest text-[#ff3f6c] bg-white/10 px-2 py-0.5 rounded-full w-fit mb-1.5">{look.tag}</span>
                        <div className="text-white font-black text-sm uppercase leading-tight">{look.title}</div>
                        <div className="text-zinc-400 text-[10px] font-semibold mt-1 group-hover:text-white transition-colors">Shop This Look →</div>
                      </div>
                    </Link>
                  ))}
                </div>
              </div>
            </div>
          );
        }

        // ══════════════════════════════════════════════════════════════════
        // 13. TESTIMONIALS
        // ══════════════════════════════════════════════════════════════════
        if (section.key === "testimonials") {
          const tList = testimonials.length ? testimonials : [
            { customer_name: "Ayesha Khan",  customer_title: "Verified Customer",  rating: 5, comment: "Absolutely love my new outfit! The fabric quality is top-notch and delivery was super fast.", avatar_url: "https://i.pravatar.cc/100?img=1" },
            { customer_name: "Ali Hassan",   customer_title: "Regular Shopper",    rating: 5, comment: "Best online shopping experience in Pakistan. AURA never disappoints!", avatar_url: "https://i.pravatar.cc/100?img=3" },
            { customer_name: "Sara Ahmed",   customer_title: "Premium Member",     rating: 5, comment: "The ethnic wear collection is stunning. My sherwani arrived perfectly packed and on time.", avatar_url: "https://i.pravatar.cc/100?img=5" },
            { customer_name: "Usman Malik",  customer_title: "Style Enthusiast",   rating: 5, comment: "Great value for money. The GenZ collection is fire! Will definitely order again.", avatar_url: "https://i.pravatar.cc/100?img=7" },
          ];
          return (
            <div key={section.id} className={wrap} style={{ background: section.background?.color ?? "#fdf2f4" }}>
              <SectionHead title={section.title} subtitle={section.subtitle} />
              {/* Active testimonial large */}
              <div className="flex flex-col items-center text-center mb-8 px-4">
                {tList[testimonialIdx] && (
                  <div className="max-w-2xl transition-all duration-500">
                    <div className="text-3xl text-[#ff3f6c] mb-3">"</div>
                    <p className="text-zinc-700 text-sm sm:text-base font-medium leading-relaxed italic">
                      {tList[testimonialIdx].comment}
                    </p>
                    <div className="flex justify-center gap-0.5 mt-4 mb-3">
                      {Array.from({ length: tList[testimonialIdx].rating ?? 5 }).map((_, i) => (
                        <span key={i} className="text-yellow-400 text-lg">★</span>
                      ))}
                    </div>
                    {tList[testimonialIdx].avatar_url && (
                      <Image src={tList[testimonialIdx].avatar_url} alt={tList[testimonialIdx].customer_name}
                        width={52} height={52} className="rounded-full mx-auto mb-2 border-2 border-[#ff3f6c]" />
                    )}
                    <div className="font-black text-sm text-zinc-900">{tList[testimonialIdx].customer_name}</div>
                    <div className="text-xs text-zinc-500">{tList[testimonialIdx].customer_title}</div>
                  </div>
                )}
              </div>
              {/* Dot indicators */}
              <div className="flex justify-center gap-2">
                {tList.map((_: any, i: number) => (
                  <button key={i} onClick={() => setTestimonialIdx(i)}
                    className={`h-2 rounded-full transition-all cursor-pointer ${testimonialIdx === i ? "w-6 bg-[#ff3f6c]" : "w-2 bg-zinc-300 hover:bg-zinc-400"}`} />
                ))}
              </div>
              {/* Mini cards row */}
              <div className="grid grid-cols-2 sm:grid-cols-4 gap-3 mt-8">
                {tList.slice(0, 4).map((t: any, i: number) => (
                  <div key={i} onClick={() => setTestimonialIdx(i)}
                    className={`bg-white rounded-xl p-4 text-center cursor-pointer border-2 transition-all ${testimonialIdx === i ? "border-[#ff3f6c] shadow-md" : "border-transparent hover:border-zinc-200"}`}>
                    {t.avatar_url && (
                      <Image src={t.avatar_url} alt={t.customer_name} width={36} height={36}
                        className="rounded-full mx-auto mb-2" />
                    )}
                    <div className="text-xs font-black text-zinc-800">{t.customer_name}</div>
                    <div className="text-[10px] text-zinc-400">{t.customer_title}</div>
                    <div className="text-yellow-400 text-xs mt-1">{"★".repeat(t.rating ?? 5)}</div>
                  </div>
                ))}
              </div>
            </div>
          );
        }

        // ══════════════════════════════════════════════════════════════════
        // 14. NEWSLETTER
        // ══════════════════════════════════════════════════════════════════
        if (section.key === "newsletter") {
          const coupon        = section.settings?.coupon_code ?? "WELCOME300";
          const discountText  = section.settings?.discount_text ?? "Rs 300 OFF your first order";
          return (
            <div key={section.id} className={`w-full ${section.padding ?? "py-16"} ${vis}`}
              style={{ background: section.background?.color ?? "#1a1a1a" }}>
              <div className="mx-auto max-w-2xl px-4 text-center">
                <div className="text-4xl mb-3">✉️</div>
                <h2 className="font-heading text-xl sm:text-2xl font-black uppercase tracking-widest text-white mb-2">
                  {section.title}
                </h2>
                {section.subtitle && (
                  <p className="text-zinc-400 text-sm font-medium mb-1">{section.subtitle}</p>
                )}
                <p className="text-[#ff3f6c] font-black text-sm mb-6">{discountText}</p>
                <form onSubmit={e => { e.preventDefault(); }} className="flex gap-2 max-w-md mx-auto mb-4">
                  <input type="email" placeholder="Enter your email address" required
                    className="flex-1 px-4 py-3 bg-white/10 border border-white/20 text-white placeholder:text-zinc-500 text-sm rounded-sm focus:outline-none focus:border-[#ff3f6c] transition-colors" />
                  <button type="submit"
                    className="bg-[#ff3f6c] hover:bg-[#e0315a] text-white font-black text-xs uppercase tracking-widest px-5 py-3 rounded-sm transition-colors whitespace-nowrap">
                    {section.button_text ?? "SUBSCRIBE"}
                  </button>
                </form>
                <div className="inline-flex items-center gap-2 bg-white/5 border border-white/10 rounded-lg px-4 py-2">
                  <span className="text-zinc-400 text-[10px] font-bold uppercase">Coupon:</span>
                  <span className="text-white font-black text-sm tracking-widest">{coupon}</span>
                </div>
                <p className="text-zinc-600 text-[10px] mt-3">No spam. Unsubscribe anytime. T&amp;C apply.</p>
              </div>
            </div>
          );
        }

        // ══════════════════════════════════════════════════════════════════
        // CUSTOM HTML
        // ══════════════════════════════════════════════════════════════════
        if (section.key === "custom_html") {
          return (
            <div key={section.id} className={wrap}
              dangerouslySetInnerHTML={{ __html: section.settings?.html ?? "" }} />
          );
        }

        return null;
      })}
    </div>
  );
}
