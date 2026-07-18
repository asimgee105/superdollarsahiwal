"use client";

import * as React from "react";
import { Tag, Check, Copy } from "lucide-react";
import { toast } from "sonner";

export default function CouponsPage() {
  const coupons = [
    { code: "AURA20", discount: "20% OFF", desc: "Applicable on orders above Rs. 2,500. Valid on topwear.", expiry: "July 31, 2026" },
    { code: "FIRSTORDER", discount: "Rs. 500 OFF", desc: "For new accounts on their first order. Minimum value Rs. 1,500.", expiry: "Dec 31, 2026" },
    { code: "MYNTRASAVE", discount: "25% OFF", desc: "Ultimate site-wide promotion, max discount up to Rs. 1,000.", expiry: "August 15, 2026" },
  ];

  const handleCopy = (code: string) => {
    navigator.clipboard.writeText(code);
    toast.success(`Coupon code ${code} copied to clipboard!`);
  };

  return (
    <div className="text-zinc-800 text-left space-y-6">
      
      {/* Page Header */}
      <div className="flex items-center gap-3 pb-4 border-b border-zinc-100">
        <Tag className="h-6 w-6 text-[#ff3f6c]" />
        <h1 className="text-lg font-black uppercase tracking-wider text-zinc-950">
          Available Coupons
        </h1>
      </div>

      <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
        {coupons.map((coupon) => (
          <div key={coupon.code} className="border border-dashed border-zinc-300 rounded-xl p-4 bg-zinc-50/50 flex flex-col justify-between gap-4 shadow-3xs relative overflow-hidden">
            
            <div className="space-y-1">
              <span className="inline-block px-2.5 py-0.5 bg-[#ff3f6c]/10 text-[#ff3f6c] border border-[#ff3f6c]/15 text-[9px] font-black uppercase tracking-wider rounded-sm">
                {coupon.discount}
              </span>
              <h3 className="text-xs font-black uppercase text-zinc-800 tracking-wider mt-2">{coupon.code}</h3>
              <p className="text-[10px] text-zinc-500 font-bold leading-relaxed">{coupon.desc}</p>
            </div>

            <div className="border-t border-zinc-150/70 pt-3 flex justify-between items-center text-[10px] font-semibold text-zinc-400">
              <span>Expires: {coupon.expiry}</span>
              <button
                onClick={() => handleCopy(coupon.code)}
                className="flex items-center gap-1 text-[#ff3f6c] font-black uppercase tracking-wider hover:underline cursor-pointer"
              >
                <Copy className="h-3 w-3" /> Copy Code
              </button>
            </div>

          </div>
        ))}
      </div>

    </div>
  );
}
