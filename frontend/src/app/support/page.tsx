"use client";

import * as React from "react";
import Link from "next/link";
import { LifeBuoy, ArrowLeft, Search, HelpCircle, FileText, Settings } from "lucide-react";
import { toast } from "sonner";
import { getRelativePath } from "@/lib/utils";

export default function SupportPage() {
  const [query, setQuery] = React.useState("");

  const guides = [
    { title: "Ordering & Checkout Guides", desc: "Step by step instructions on placing and managing orders." },
    { title: "Payment & Promos Matrix", desc: "Understanding promo codes, local bank discounts, and Stripe payments." },
    { title: "Delivery & Courier Schedules", desc: "Delivery time frames, courier partners, and tracking steps." },
    { title: "Returns & Exchanges Help", desc: "Initiating a return request and tracking refund status." }
  ];

  return (
    <main className="min-h-screen bg-zinc-50/50 py-12 px-4 sm:px-6 lg:px-8 font-sans select-none">
      <div className="max-w-4xl mx-auto space-y-6">
        
        {/* Back Link */}
        <Link href={getRelativePath("/")} className="inline-flex items-center gap-1.5 text-[11px] font-black uppercase tracking-wider text-zinc-450 hover:text-zinc-800 transition-colors">
          <ArrowLeft className="h-3.5 w-3.5" /> Back to Storefront
        </Link>

        {/* Hero Card */}
        <div className="bg-white border border-zinc-150 rounded-2xl p-6 sm:p-8 shadow-3xs space-y-6">
          <div className="flex items-center gap-3 border-b border-zinc-100 pb-5">
            <div className="p-2.5 bg-[#f51c50]/5 rounded-xl text-[#f51c50]">
              <LifeBuoy className="h-5 w-5" />
            </div>
            <div>
              <span className="text-[10px] font-black uppercase tracking-widest text-[#f51c50]">Help Center</span>
              <h1 className="text-lg font-black text-zinc-900 uppercase tracking-wider mt-0.5">Support Desk</h1>
            </div>
          </div>

          {/* Search bar */}
          <div className="relative max-w-xl">
            <Search className="absolute left-4 top-3.5 h-4 w-4 text-zinc-400" />
            <input 
              type="text" 
              value={query}
              onChange={(e) => setQuery(e.target.value)}
              placeholder="Search help guides and articles..."
              className="w-full text-xs font-semibold pl-11 pr-4 py-3.5 bg-zinc-50 border border-zinc-200 rounded-xl focus:outline-none focus:border-[#f51c50] transition-colors"
            />
          </div>

          {/* Guides grid */}
          <div className="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-4">
            {guides.map((guide, idx) => (
              <div key={idx} className="border border-zinc-150 rounded-xl p-5 hover:border-[#f51c50]/30 transition-colors cursor-pointer space-y-2">
                <h3 className="text-xs font-black uppercase tracking-wide text-zinc-850 flex items-center gap-2">
                  <FileText className="h-4 w-4 text-[#f51c50]" /> {guide.title}
                </h3>
                <p className="text-xs text-zinc-500 font-medium leading-relaxed">
                  {guide.desc}
                </p>
              </div>
            ))}
          </div>

        </div>
      </div>
    </main>
  );
}
