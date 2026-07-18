"use client";

import * as React from "react";
import Link from "next/link";
import { HelpCircle, ChevronDown, ChevronUp, ArrowLeft } from "lucide-react";
import { getRelativePath } from "@/lib/utils";

export default function FAQPage() {
  const [openIndex, setOpenIndex] = React.useState<number | null>(0);

  const faqs = [
    {
      q: "How can I track my order shipment status?",
      a: "Simply visit the Track Orders route, input your 10-digit tracking code (e.g. AURA-TRK-XXXXXXXXX) matching your order confirmation receipt, and review the live tracking timeline."
    },
    {
      q: "What is your package return and exchange policy?",
      a: "We offer a hassle-free 14-day return policy. Items must be unused, unwashed, and returned in original packages with labels/tags intact. Exchanges can be initiated from the admin profile panel."
    },
    {
      q: "What payment gateways are supported during checkout?",
      a: "We support multiple secure checkout payment options including Cash on Delivery (COD), Stripe Online Card payments, PayPal Express, Google Pay, and Apple Pay."
    },
    {
      q: "Are the products sold on AURA authentic and original?",
      a: "Yes! All fashion, apparel, and lifestyle items hosted on AURA are 100% authentic and original, direct from our production warehouses and collaborating designers."
    },
    {
      q: "How can I contact customer support?",
      a: "Our customer success desk is available via phone support at 0300-1234567, email at support@aura.com, or through the Contact feedback form on our site."
    }
  ];

  return (
    <main className="min-h-screen bg-zinc-50/50 py-12 px-4 sm:px-6 lg:px-8 font-sans select-none">
      <div className="max-w-3xl mx-auto space-y-6">
        
        {/* Back Link */}
        <Link href={getRelativePath("/")} className="inline-flex items-center gap-1.5 text-[11px] font-black uppercase tracking-wider text-zinc-450 hover:text-zinc-800 transition-colors">
          <ArrowLeft className="h-3.5 w-3.5" /> Back to Storefront
        </Link>

        {/* Content Card */}
        <div className="bg-white border border-zinc-150 rounded-2xl p-6 sm:p-8 shadow-3xs space-y-6">
          <div className="flex items-center gap-3 border-b border-zinc-100 pb-5">
            <div className="p-2.5 bg-[#f51c50]/5 rounded-xl text-[#f51c50]">
              <HelpCircle className="h-5 w-5" />
            </div>
            <div>
              <span className="text-[10px] font-black uppercase tracking-widest text-[#f51c50]">Help Center</span>
              <h1 className="text-lg font-black text-zinc-900 uppercase tracking-wider mt-0.5">Frequently Asked Questions</h1>
            </div>
          </div>

          <div className="space-y-4 pt-2">
            {faqs.map((faq, idx) => {
              const isOpen = openIndex === idx;
              return (
                <div key={idx} className="border border-zinc-150 rounded-xl overflow-hidden transition-all duration-300">
                  <button
                    onClick={() => setOpenIndex(isOpen ? null : idx)}
                    className="w-full flex items-center justify-between p-4 text-left font-black text-zinc-850 hover:bg-zinc-50 transition-colors text-xs sm:text-sm uppercase tracking-wide cursor-pointer"
                  >
                    <span>{faq.q}</span>
                    {isOpen ? <ChevronUp className="h-4 w-4 text-[#f51c50]" /> : <ChevronDown className="h-4 w-4 text-zinc-400" />}
                  </button>
                  {isOpen && (
                    <div className="px-4 pb-4 pt-1.5 text-zinc-600 font-medium text-xs sm:text-sm leading-relaxed border-t border-zinc-100 bg-zinc-50/20">
                      {faq.a}
                    </div>
                  )}
                </div>
              );
            })}
          </div>
        </div>
      </div>
    </main>
  );
}
