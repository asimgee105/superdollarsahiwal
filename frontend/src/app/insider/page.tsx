"use client";

import * as React from "react";
import { Sparkles, Trophy, Gift, ArrowRight, ShieldCheck, Ticket } from "lucide-react";
import { toast } from "sonner";
import { Button } from "@/components/ui/button";

export default function InsiderPage() {
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

  const handleClaimReward = (title: string, cost: number) => {
    toast.success(`Successfully claimed: ${title}! ${cost} points deducted.`);
  };

  return (
    <div className="min-h-screen bg-zinc-50/50 py-12 px-4 sm:px-6 lg:px-8 select-none text-zinc-800 text-left">
      <div className="max-w-4xl mx-auto space-y-10">
        
        {/* Hero Card */}
        <div className="bg-gradient-to-br from-zinc-900 to-zinc-950 rounded-2xl p-8 sm:p-12 text-white flex flex-col md:flex-row justify-between items-center gap-8 shadow-md border border-zinc-800 relative overflow-hidden">
          <div className="absolute top-0 right-0 w-64 h-64 bg-[#ff3f6c]/10 rounded-full blur-3xl pointer-events-none"></div>
          <div className="space-y-4 max-w-lg z-10">
            <div className="inline-flex items-center gap-2 px-3 py-1 bg-[#ff3f6c]/10 border border-[#ff3f6c]/25 rounded-full text-[#ff3f6c] text-[10px] font-black uppercase tracking-wider">
              <Sparkles className="h-3 w-3" /> Exclusive Loyalty Club
            </div>
            <h1 className="text-3xl sm:text-4xl font-black uppercase tracking-wider">{siteName} Insider</h1>
            <p className="text-xs sm:text-sm font-semibold text-zinc-400 leading-relaxed">
              Unlock the ultimate shopping experience. Earn points on every purchase, access early product drops, enjoy free express shipping, and claim premium rewards.
            </p>
          </div>
          <div className="bg-zinc-850 border border-zinc-850 rounded-xl p-5 w-52 shrink-0 z-10 text-center shadow-lg">
            <p className="text-[10px] font-black text-zinc-500 uppercase tracking-widest">Your Points Balance</p>
            <h3 className="text-3xl font-black text-[#ff3f6c] mt-1">450</h3>
            <span className="inline-block mt-3 px-2.5 py-0.5 bg-zinc-750 text-white text-[8px] font-black uppercase tracking-wider rounded-full">
              Insider Tier
            </span>
          </div>
        </div>

        {/* Perks Grid */}
        <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
          <div className="bg-white border border-zinc-150 rounded-2xl p-5 shadow-3xs flex gap-4">
            <div className="p-3 bg-[#ff3f6c]/5 border border-[#ff3f6c]/10 rounded-xl text-[#ff3f6c] shrink-0 h-11 w-11 flex items-center justify-center">
              <Trophy className="h-5 w-5" />
            </div>
            <div>
              <h3 className="text-xs font-black uppercase tracking-wider text-zinc-900">VIP Tiers</h3>
              <p className="text-[10px] text-zinc-450 font-semibold leading-relaxed mt-1">
                Progress through Insider, Select, and Elite levels to unlock progressively larger discounts.
              </p>
            </div>
          </div>
          <div className="bg-white border border-zinc-150 rounded-2xl p-5 shadow-3xs flex gap-4">
            <div className="p-3 bg-[#ff3f6c]/5 border border-[#ff3f6c]/10 rounded-xl text-[#ff3f6c] shrink-0 h-11 w-11 flex items-center justify-center">
              <Gift className="h-5 w-5" />
            </div>
            <div>
              <h3 className="text-xs font-black uppercase tracking-wider text-zinc-900">Point Rewards</h3>
              <p className="text-[10px] text-zinc-450 font-semibold leading-relaxed mt-1">
                Redeem accumulated loyalty points for vouchers, free shipping coupons, or partner offers.
              </p>
            </div>
          </div>
          <div className="bg-white border border-zinc-150 rounded-2xl p-5 shadow-3xs flex gap-4">
            <div className="p-3 bg-[#ff3f6c]/5 border border-[#ff3f6c]/10 rounded-xl text-[#ff3f6c] shrink-0 h-11 w-11 flex items-center justify-center">
              <Sparkles className="h-5 w-5" />
            </div>
            <div>
              <h3 className="text-xs font-black uppercase tracking-wider text-zinc-900">Priority Drops</h3>
              <p className="text-[10px] text-zinc-450 font-semibold leading-relaxed mt-1">
                Shop select brand collections and clearance sales 24 hours before normal users.
              </p>
            </div>
          </div>
        </div>

        {/* Claim Rewards */}
        <div className="bg-white border border-zinc-150 rounded-2xl p-6 sm:p-8 shadow-3xs space-y-6">
          <h2 className="text-md font-black uppercase tracking-wider text-zinc-900 border-b border-zinc-100 pb-4">
            Redeem Points for Vouchers
          </h2>
          <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
            
            <div className="border border-zinc-150 rounded-xl p-4 flex justify-between items-center shadow-3xs hover:border-[#ff3f6c] transition-all">
              <div className="flex gap-3.5 items-center">
                <div className="p-2.5 bg-yellow-50 text-yellow-600 rounded-lg">
                  <Ticket className="h-5 w-5" />
                </div>
                <div>
                  <h4 className="text-xs font-black uppercase text-zinc-800">Rs. 500 Store Voucher</h4>
                  <p className="text-[10px] font-bold text-zinc-400 uppercase mt-0.5">Costs 200 Points</p>
                </div>
              </div>
              <button 
                onClick={() => handleClaimReward("Rs. 500 Store Voucher", 200)}
                className="px-3.5 py-2 border border-zinc-200 hover:border-[#ff3f6c] hover:bg-[#ff3f6c]/5 rounded-lg text-[10px] font-black uppercase tracking-wider text-zinc-700 transition-all cursor-pointer"
              >
                Claim
              </button>
            </div>

            <div className="border border-zinc-150 rounded-xl p-4 flex justify-between items-center shadow-3xs hover:border-[#ff3f6c] transition-all">
              <div className="flex gap-3.5 items-center">
                <div className="p-2.5 bg-emerald-50 text-emerald-600 rounded-lg">
                  <Ticket className="h-5 w-5" />
                </div>
                <div>
                  <h4 className="text-xs font-black uppercase text-zinc-800">Free Express Delivery</h4>
                  <p className="text-[10px] font-bold text-zinc-400 uppercase mt-0.5">Costs 100 Points</p>
                </div>
              </div>
              <button 
                onClick={() => handleClaimReward("Free Express Delivery", 100)}
                className="px-3.5 py-2 border border-zinc-200 hover:border-[#ff3f6c] hover:bg-[#ff3f6c]/5 rounded-lg text-[10px] font-black uppercase tracking-wider text-zinc-700 transition-all cursor-pointer"
              >
                Claim
              </button>
            </div>

          </div>
        </div>

      </div>
    </div>
  );
}
