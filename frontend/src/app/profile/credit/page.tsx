"use client";

import * as React from "react";
import { Wallet, Plus, ArrowRight } from "lucide-react";
import { toast } from "sonner";
import { Button } from "@/components/ui/button";

export default function ProfileCreditPage() {
  const [redeemCode, setRedeemCode] = React.useState("");
  const [balance, setBalance] = React.useState(1200); // Mock starting balance

  const handleRedeem = (e: React.FormEvent) => {
    e.preventDefault();
    if (!redeemCode.trim()) {
      toast.warning("Please enter a valid gift code.");
      return;
    }
    toast.success("Checking redemption code...");
    setTimeout(() => {
      setBalance((prev) => prev + 1000);
      toast.success("Rs. 1,000 credit added successfully to your wallet!");
      setRedeemCode("");
    }, 1200);
  };

  return (
    <div className="text-zinc-800 text-left space-y-6">
      
      {/* Page Header */}
      <div className="flex items-center gap-3 pb-4 border-b border-zinc-100">
        <Wallet className="h-6 w-6 text-[#ff3f6c]" />
        <h1 className="text-lg font-black uppercase tracking-wider text-zinc-950">
          Store Credit Balance
        </h1>
      </div>

      <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
        
        {/* Wallet details */}
        <div className="border border-zinc-150 rounded-xl p-5 shadow-3xs flex flex-col justify-between min-h-[160px] bg-zinc-50/50">
          <div>
            <p className="text-[10px] font-black text-zinc-400 uppercase tracking-widest">Available Wallet Credit</p>
            <h2 className="text-3xl font-black text-[#ff3f6c] mt-1.5">Rs. {balance.toLocaleString()}.00</h2>
          </div>
          <p className="text-[10px] text-zinc-450 font-semibold leading-relaxed">
            Use this balance directly at checkout to purchase products instantly without entering card details.
          </p>
        </div>

        {/* Redeem code */}
        <div className="border border-zinc-150 rounded-xl p-5 shadow-3xs space-y-4 bg-white">
          <h3 className="text-xs font-black uppercase tracking-wider text-zinc-800">Redeem Gift Card Code</h3>
          <form onSubmit={handleRedeem} className="flex gap-2">
            <input
              type="text"
              placeholder="Enter 16-digit voucher PIN"
              value={redeemCode}
              onChange={(e) => setRedeemCode(e.target.value)}
              className="flex-grow text-xs font-semibold px-4 py-2.5 bg-zinc-50 border border-zinc-200 rounded-lg focus:outline-none focus:border-[#ff3f6c] transition-colors"
              required
            />
            <button
              type="submit"
              className="px-4 py-2.5 bg-zinc-800 hover:bg-zinc-950 text-white text-[10px] font-black uppercase tracking-wider rounded-lg transition-all cursor-pointer shrink-0"
            >
              Redeem
            </button>
          </form>
          <p className="text-[9px] text-zinc-400 font-semibold">
            Credit is added instantly to your balance upon code verification.
          </p>
        </div>

      </div>

      {/* Transaction History */}
      <div className="space-y-4">
        <h3 className="text-xs font-black uppercase tracking-wider text-zinc-850 border-b border-zinc-100 pb-2">
          Transaction History
        </h3>
        <div className="divide-y divide-zinc-100 text-xs">
          <div className="py-3 flex justify-between items-center">
            <div>
              <h4 className="font-extrabold text-zinc-800">Wallet Sign-up Bonus</h4>
              <p className="text-[10px] text-zinc-400 font-semibold mt-0.5">July 14, 2026</p>
            </div>
            <span className="font-black text-emerald-600">+ Rs. 1,000</span>
          </div>
          <div className="py-3 flex justify-between items-center">
            <div>
              <h4 className="font-extrabold text-zinc-800">Referral Credit Received</h4>
              <p className="text-[10px] text-zinc-400 font-semibold mt-0.5">July 15, 2026</p>
            </div>
            <span className="font-black text-emerald-600">+ Rs. 200</span>
          </div>
        </div>
      </div>

    </div>
  );
}
