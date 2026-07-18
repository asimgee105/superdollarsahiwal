"use client";

import * as React from "react";
import Link from "next/link";
import Image from "next/image";
import { getRelativePath } from "@/lib/utils";

interface Section {
  id: number;
  key: string;
  title: string;
  subtitle: string | null;
  description: string | null;
  background: {
    type: string;
    color: string | null;
    image: string | null;
    video: string | null;
  };
  padding: string | null;
  margin: string | null;
  width: string;
  animation: string | null;
  button_text: string | null;
  button_url: string | null;
  layout_variation: string;
  show_on_mobile: boolean;
  show_on_desktop: boolean;
  settings: any;
}

export default function Home() {
  const [sections, setSections] = React.useState<Section[]>([]);
  const [layoutInfo, setLayoutInfo] = React.useState<any>(null);
  const [currentSlide, setCurrentSlide] = React.useState(0);
  const [cardSlide, setCardSlide] = React.useState(0);

  // Mouse Drag / Touch Swipe states
  const [dragStart, setDragStart] = React.useState<number | null>(null);
  const [dragOffset, setDragOffset] = React.useState(0);

  const API_URL = process.env.NEXT_PUBLIC_API_URL || "http://localhost:8000";

  // Fetch Homepage Layout and Section configs
  React.useEffect(() => {
    // 1. Fetch Dynamic Theme Styling colors
    fetch(`${API_URL}/api/v1/theme`)
      .then((res) => {
        if (!res.ok) throw new Error();
        return res.json();
      })
      .then((data) => {
        if (data.colors) {
          const root = document.documentElement;
          root.style.setProperty("--primary-color", data.colors.primary);
          root.style.setProperty("--secondary-color", data.colors.secondary);
        }
      })
      .catch(() => {
        // Fallback styling if API is not yet loaded
        const root = document.documentElement;
        root.style.setProperty("--primary-color", "#ff3f6c");
        root.style.setProperty("--secondary-color", "#1a1a1a");
      });

    // 2. Fetch Homepage Sections Builder
    fetch(`${API_URL}/api/v1/homepage`)
      .then((res) => {
        if (!res.ok) throw new Error();
        return res.json();
      })
      .then((data) => {
        setLayoutInfo(data);
        if (data.sections) {
          setSections(data.sections);
        }
      })
      .catch(() => {
        // Fallback local structures if API server is offline
        setSections([
          {
            id: 1,
            key: "hero_slider",
            title: "Fallback Slider",
            subtitle: null,
            description: null,
            background: { type: "color", color: null, image: null, video: null },
            padding: null,
            margin: null,
            width: "full",
            animation: null,
            button_text: null,
            button_url: null,
            layout_variation: "default",
            show_on_mobile: true,
            show_on_desktop: true,
            settings: {
              slides: [
                {
                  bg: "from-[#fae04b] via-[#fbd83b] to-[#fae66d]",
                  logoText: "fwd",
                  logoColor: "text-zinc-950",
                  title: "Gen-Z Fashion For All",
                  subtitle: "UNDER",
                  price: "Rs 999",
                  btnLabel: "SHOP NOW >",
                  images: [
                    "https://images.unsplash.com/photo-1617137968427-85924c800a22?q=80&w=400&auto=format&fit=crop",
                    "https://images.unsplash.com/photo-1494790108377-be9c29b29330?q=80&w=400&auto=format&fit=crop"
                  ]
                }
              ]
            }
          }
        ]);
      });
  }, [API_URL]);

  // Timers for Hero slide animations
  const activeHeroSection = sections.find((s) => s.key === "hero_slider");
  const slidesCount = activeHeroSection?.settings?.slides?.length || 1;

  React.useEffect(() => {
    if (slidesCount <= 1) return;
    const timer = setInterval(() => {
      setCurrentSlide((prev) => (prev + 1) % slidesCount);
    }, 4500);
    return () => clearInterval(timer);
  }, [slidesCount]);

  // Timers for Bank Card rotation animations
  const activeBankSection = sections.find((s) => s.key === "bank_offers");
  const offersCount = activeBankSection?.settings?.offers?.length || 1;

  React.useEffect(() => {
    if (offersCount <= 1) return;
    const cardTimer = setInterval(() => {
      setCardSlide((prev) => (prev + 1) % offersCount);
    }, 2000);
    return () => clearInterval(cardTimer);
  }, [offersCount]);

  // Swiping controls
  const handleDragStart = (clientX: number) => {
    setDragStart(clientX);
  };

  const handleDragMove = (clientX: number) => {
    if (dragStart === null) return;
    setDragOffset(clientX - dragStart);
  };

  const handleDragEnd = () => {
    if (dragStart === null) return;
    if (dragOffset > 50) {
      setCurrentSlide((prev) => (prev - 1 + slidesCount) % slidesCount);
    } else if (dragOffset < -50) {
      setCurrentSlide((prev) => (prev + 1) % slidesCount);
    }
    setDragStart(null);
    setDragOffset(0);
  };

  return (
    <div className="flex flex-col min-h-screen bg-zinc-50/50">
      
      {/* Dynamic Announcement Line Banner */}
      <section className="w-full bg-[#f51c50] py-6 px-4 text-center text-white font-heading font-extrabold tracking-widest text-sm sm:text-base md:text-lg flex flex-col sm:flex-row items-center justify-center gap-2 sm:gap-6 shadow-sm select-none">
        <span>FLAT Rs. 500 OFF + FREE SHIPPING ON YOUR FIRST ORDER</span>
        <Link href={getRelativePath("/register")}>
          <span className="bg-white text-[#f51c50] text-[10px] sm:text-xs font-black uppercase px-4 py-1.5 rounded-sm shadow-sm hover:opacity-95 transition-opacity inline-block cursor-pointer">
            USE CODE: AURA50
          </span>
        </Link>
      </section>

      {/* Ticket Stub Promo Coupon Banner */}
      <div className="mx-auto max-w-7xl px-4 pt-8 sm:px-6 lg:px-8 w-full select-none">
        <div className="relative bg-gradient-to-r from-[#ffebe0] to-[#ffdcd0] border border-[#ffcdb3] rounded-xl py-5 px-6 sm:px-10 flex flex-col md:flex-row items-center justify-between gap-6 shadow-sm overflow-hidden">
          <div className="absolute top-1/2 -left-3.5 w-7 h-7 bg-[#fdf2f4] rounded-full -translate-y-1/2 border-r border-[#ffcdb3] z-10"></div>
          <div className="absolute top-1/2 -right-3.5 w-7 h-7 bg-[#fdf2f4] rounded-full -translate-y-1/2 border-l border-[#ffcdb3] z-10"></div>
          
          <div className="flex flex-col text-center md:text-left gap-1 z-10">
            <h2 className="text-2xl sm:text-3xl font-black text-[#ff3f6c] tracking-wide">
              <span className="text-[#ff5d24]">Get 25% Off</span>
            </h2>
            <p className="text-sm sm:text-base text-zinc-700 font-extrabold tracking-wide">
              Up To Rs 200 Off*
            </p>
          </div>

          <div className="flex flex-col items-center gap-2 z-10">
            <div className="bg-white border border-[#ffbca0] rounded-lg px-6 py-2.5 flex items-center gap-3 shadow-xs">
              <div className="flex flex-col text-center">
                <span className="text-[9px] font-black uppercase text-zinc-400 leading-none">COUPON</span>
                <span className="text-[10px] font-black uppercase text-zinc-400 leading-none mt-0.5">CODE</span>
              </div>
              <span className="text-lg font-black text-zinc-800 tracking-wider">
                MYNTRASAVE
              </span>
            </div>
            <span className="text-[10px] text-zinc-500 font-bold tracking-wide">
              On Your First Order | T&C Apply
            </span>
          </div>

          <div className="hidden md:flex items-center justify-center w-14 h-14 bg-white/40 rounded-full border border-[#ffa880]/30 z-10">
            <span className="text-2xl font-black text-[#ff5d24]">%</span>
          </div>
        </div>
      </div>

      {/* Dynamic Sections Engine */}
      {sections.map((section) => {
        // Mobile / Desktop visibility rules
        const visibilityClass = `
          ${!section.show_on_mobile ? "hidden md:block" : ""}
          ${!section.show_on_desktop ? "block md:hidden" : ""}
        `.trim();

        // 1. Render Hero Slider Section
        if (section.key === "hero_slider") {
          const slides = section.settings?.slides || [];
          if (slides.length === 0) return null;

          return (
            <section key={section.id} className={`mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8 w-full select-none ${visibilityClass}`}>
              <div
                onMouseDown={(e) => handleDragStart(e.clientX)}
                onMouseMove={(e) => handleDragMove(e.clientX)}
                onMouseUp={handleDragEnd}
                onMouseLeave={handleDragEnd}
                onTouchStart={(e) => handleDragStart(e.touches[0].clientX)}
                onTouchMove={(e) => handleDragMove(e.touches[0].clientX)}
                onTouchEnd={handleDragEnd}
                className="relative h-[250px] sm:h-[350px] md:h-[400px] w-full rounded-xl overflow-hidden shadow-md cursor-grab active:cursor-grabbing"
              >
                {slides.map((slide: any, index: number) => (
                  <div
                    key={index}
                    className={`absolute inset-0 bg-gradient-to-r ${slide.bg || "from-zinc-100 to-zinc-200"} flex items-center justify-between px-6 sm:px-12 md:px-20 transition-opacity duration-1000 ${
                      currentSlide === index ? "opacity-100 z-10" : "opacity-0 z-0"
                    }`}
                  >
                    {/* Floating model assets */}
                    <div className="relative h-full w-[35%] sm:w-[45%] flex items-end pointer-events-none">
                      {slide.images?.[0] && (
                        <div className="absolute left-0 bottom-0 w-[70%] h-[80%] rounded-t-lg overflow-hidden border-2 border-white/20">
                          <Image src={slide.images[0]} alt="Model" fill priority className="object-cover object-top" />
                        </div>
                      )}
                      {slide.images?.[1] && (
                        <div className="absolute left-[30%] bottom-0 w-[70%] h-[90%] rounded-t-lg overflow-hidden border-2 border-white/40 shadow-lg">
                          <Image src={slide.images[1]} alt="Model" fill priority className="object-cover object-top" />
                        </div>
                      )}
                    </div>

                    {/* Styled central logo */}
                    <div className="absolute inset-0 flex items-center justify-center pointer-events-none z-10">
                      <span className={`text-[12vw] sm:text-[10vw] font-black italic tracking-tighter uppercase opacity-30 ${slide.logoColor || "text-zinc-500"}`}>
                        {slide.logoText || "fwd"}
                      </span>
                    </div>

                    {/* Text promotions */}
                    <div className="flex flex-col items-end text-right z-20 max-w-[50%] sm:max-w-[40%]">
                      <h2 className="text-zinc-950 font-black text-lg sm:text-3xl md:text-4xl uppercase tracking-wider leading-none">
                        {slide.title}
                      </h2>
                      <div className="flex items-baseline gap-2 mt-2 sm:mt-4">
                        <span className="text-zinc-950 font-black text-xs sm:text-base uppercase tracking-wide">
                          {slide.subtitle || "UNDER"}
                        </span>
                        <span className="text-zinc-950 font-extrabold text-xl sm:text-4xl md:text-5xl tracking-wide">
                          {slide.price}
                        </span>
                      </div>
                      <Link href={getRelativePath("/catalog/")} className="mt-4 sm:mt-6 z-30">
                        <span className="bg-white text-zinc-950 font-black text-[10px] sm:text-xs uppercase tracking-widest px-5 py-2.5 sm:px-8 sm:py-3.5 hover:bg-zinc-100 transition-colors shadow-sm rounded-sm inline-block cursor-pointer">
                          {slide.btnLabel || "SHOP NOW"}
                        </span>
                      </Link>
                    </div>
                  </div>
                ))}

                {/* Arrows */}
                <button
                  onClick={() => setCurrentSlide((prev) => (prev - 1 + slides.length) % slides.length)}
                  className="absolute left-4 top-1/2 -translate-y-1/2 w-9 h-9 rounded-full bg-white/70 hover:bg-white text-zinc-800 flex items-center justify-center font-bold shadow-sm z-30 transition-colors cursor-pointer"
                >
                  &larr;
                </button>
                <button
                  onClick={() => setCurrentSlide((prev) => (prev + 1) % slides.length)}
                  className="absolute right-4 top-1/2 -translate-y-1/2 w-9 h-9 rounded-full bg-white/70 hover:bg-white text-zinc-800 flex items-center justify-center font-bold shadow-sm z-30 transition-colors cursor-pointer"
                >
                  &rarr;
                </button>

                {/* Dots */}
                <div className="absolute bottom-4 left-1/2 -translate-x-1/2 flex gap-2 z-30">
                  {slides.map((_: any, idx: number) => (
                    <button
                      key={idx}
                      onClick={() => setCurrentSlide(idx)}
                      className={`w-2.5 h-2.5 rounded-full transition-all cursor-pointer ${
                        currentSlide === idx ? "bg-[#ff3f6c] w-6" : "bg-white/60 hover:bg-white"
                      }`}
                    />
                  ))}
                </div>
              </div>
            </section>
          );
        }

        // 2. Render Bank Offer Banner Section
        if (section.key === "bank_offers") {
          const offers = section.settings?.offers || [];
          if (offers.length === 0) return null;

          return (
            <section key={section.id} className={`mx-auto max-w-7xl px-4 pb-8 sm:px-6 lg:px-8 w-full select-none ${visibilityClass}`}>
              <div className="flex gap-1.5 justify-center mb-3">
                {offers.map((_: any, i: number) => (
                  <span
                    key={i}
                    className={`h-1.5 rounded-full transition-all duration-300 ${
                      cardSlide === i ? "w-4 bg-zinc-700" : "w-1.5 bg-zinc-200"
                    }`}
                  />
                ))}
              </div>

              <div className="relative border border-[#d6c7ff] rounded-2xl bg-white overflow-hidden shadow-xs h-20 sm:h-24 w-full">
                <div className="absolute right-[12%] sm:right-[10%] top-0 bottom-0 border-l border-dashed border-zinc-200 z-10" />
                {offers.map((offer: any, index: number) => (
                  <div
                    key={index}
                    className={`absolute inset-0 flex items-center justify-between pl-4 pr-1 sm:pl-8 sm:pr-4 transition-opacity duration-500 ${
                      cardSlide === index ? "opacity-100 z-10" : "opacity-0 z-0"
                    }`}
                  >
                    {/* Brand Logos */}
                    <div className="flex items-center gap-3 w-[30%] sm:w-[25%]">
                      {offer.type === "bob-kotak" && (
                        <div className="flex items-center gap-2">
                          <span className="text-orange-600 font-extrabold text-[10px] sm:text-xs tracking-tight uppercase leading-none">BOBCARD</span>
                          <span className="bg-red-600 text-white font-black text-[9px] sm:text-[10px] px-1.5 py-0.5 rounded-xs leading-none">kotak</span>
                        </div>
                      )}
                      {offer.type === "flipkart-sbi" && (
                        <div className="flex items-center gap-1.5 relative h-8 sm:h-12 w-14 sm:w-20">
                          <div className="absolute left-0 w-8 sm:w-11 h-5 sm:h-7 bg-zinc-950 border border-zinc-800 rounded-sm shadow-xs transform -rotate-6 z-10 flex items-center justify-center text-[5px] text-white">CARD</div>
                          <div className="absolute left-4 sm:left-6 w-8 sm:w-11 h-5 sm:h-7 bg-gradient-to-r from-blue-500 to-[#ffe100] border border-blue-400 rounded-sm shadow-xs transform rotate-6 z-20 flex items-center justify-center text-[5px] text-white font-bold">SBI</div>
                        </div>
                      )}
                      {offer.type === "hdfc" && (
                        <span className="text-blue-800 font-black text-xs sm:text-base tracking-widest leading-none">HDFC BANK</span>
                      )}
                      {offer.type === "scb" && (
                        <span className="text-emerald-600 font-black text-[10px] sm:text-xs tracking-tight uppercase leading-none">Standard Chartered</span>
                      )}
                    </div>

                    <div className="flex items-center flex-grow pl-2 sm:pl-4 border-l border-zinc-200 h-8 sm:h-12">
                      <div className="flex flex-col text-left">
                        <span className="text-zinc-900 font-black text-xs sm:text-base tracking-wide">{offer.discount}</span>
                        <span className="text-zinc-400 font-bold text-[9px] sm:text-xs mt-0.5">{offer.desc}</span>
                      </div>
                    </div>

                    <div className="w-[12%] sm:w-[10%] h-full flex items-center justify-center">
                      <span className="text-[8px] sm:text-[9px] text-zinc-400 font-black tracking-widest uppercase transform rotate-90 leading-none whitespace-nowrap">*T&C Apply</span>
                    </div>
                  </div>
                ))}
              </div>
            </section>
          );
        }

        // 3. Render Budget Bargains Section
        if (section.key === "budget_bargains") {
          const items = section.settings?.items || [];
          return (
            <section key={section.id} className={`mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8 w-full ${visibilityClass}`}>
              <div className="text-center mb-8">
                <h2 className="font-heading text-lg sm:text-xl font-black uppercase tracking-widest text-foreground relative inline-block pb-2 border-b-2 border-[#f51c50]">
                  {section.title}
                </h2>
              </div>
              <div className="grid grid-cols-2 md:grid-cols-4 gap-6">
                {items.map((item: any, idx: number) => (
                  <Link key={idx} href={getRelativePath(`/catalog/?tag=${item.title.toLowerCase()}`)} className="group relative aspect-4/5 overflow-hidden rounded-md bg-muted shadow-sm hover:shadow-md transition-shadow">
                    <Image src={item.image} alt={item.title} fill sizes="(max-width: 768px) 50vw, 25vw" className="object-cover transition-transform duration-500 group-hover:scale-103" />
                    <div className="absolute inset-0 bg-gradient-to-t from-black/90 via-black/20 to-transparent p-4 flex flex-col justify-end text-white">
                      <h3 className="font-heading text-sm font-black tracking-wider uppercase">{item.title}</h3>
                      <p className="text-[9px] text-zinc-300 font-semibold tracking-wide mt-0.5">{item.tagline}</p>
                    </div>
                  </Link>
                ))}
              </div>
            </section>
          );
        }

        // 4. Render Categories Section
        if (section.key === "categories") {
          const categories = section.settings?.categories || [];
          return (
            <section key={section.id} className={`mx-auto max-w-7xl px-4 pb-16 sm:px-6 lg:px-8 w-full ${visibilityClass}`}>
              <div className="text-center mb-8">
                <h2 className="font-heading text-lg sm:text-xl font-black uppercase tracking-widest text-foreground relative inline-block pb-2 border-b-2 border-[#f51c50]">
                  {section.title}
                </h2>
              </div>
              <div className="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-6">
                {categories.map((cat: any, idx: number) => (
                  <Link
                    key={idx}
                    href={getRelativePath(`/catalog/?category=${cat.title.toLowerCase()}`)}
                    className="group border-[3px] border-[#ffd5bd] p-1.5 bg-white text-center flex flex-col hover:shadow-md transition-shadow duration-300"
                  >
                    <div className="relative aspect-[3/4] w-full bg-zinc-100 overflow-hidden">
                      <Image src={cat.image} alt={cat.title} fill sizes="(max-width: 768px) 50vw, 16vw" className="object-cover transition-transform duration-500 group-hover:scale-103" />
                    </div>
                    <div className="py-2.5 flex flex-col items-center">
                      <h3 className="text-[13px] font-black text-zinc-800 tracking-wide uppercase">{cat.title}</h3>
                      <p className="text-[16px] font-black text-zinc-950 mt-1 uppercase">{cat.discount}</p>
                      <span className="text-[11px] font-bold text-zinc-500 hover:text-[#ff3f6c] transition-colors mt-1 underline cursor-pointer">Shop Now</span>
                    </div>
                  </Link>
                ))}
              </div>
            </section>
          );
        }

        // 5. Default/Custom HTML block support
        if (section.key === "custom_html") {
          return (
            <section key={section.id} className={`mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8 w-full ${visibilityClass}`}>
              <div dangerouslySetInnerHTML={{ __html: section.settings?.html || "" }} />
            </section>
          );
        }

        return null;
      })}
    </div>
  );
}
