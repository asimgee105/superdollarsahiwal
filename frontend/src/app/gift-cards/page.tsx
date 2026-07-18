"use client";

import * as React from "react";
import { CreditCard, ArrowRight, ShieldCheck, HelpCircle } from "lucide-react";
import { toast } from "sonner";
import { Button } from "@/components/ui/button";

export default function GiftCardsPage() {
  const [cardNumber, setCardNumber] = React.useState("");
  const [pin, setPin] = React.useState("");
  const [checking, setChecking] = React.useState(false);
  const [balance, setBalance] = React.useState<number | null>(null);

  const handleCheckBalance = (e: React.FormEvent) => {
    e.preventDefault();
    if (!cardNumber || !pin) {
      toast.warning("Please fill card number and PIN.");
      return;
    }
    setChecking(true);
    setTimeout(() => {
      setChecking(false);
      // Mock balance check
      const mockBalance = Math.floor(Math.random() * 5000) + 500;
      setBalance(mockBalance);
      toast.success("Card balance retrieved successfully!");
    }, 1500);
  };

  const handleBuyCard = (amount: number) => {
    toast.success(`Redirecting to payment for Rs. ${amount} Gift Card!`);
  };

  return (
    <div className="min-h-screen bg-zinc-50/50 py-12 px-4 sm:px-6 lg:px-8 select-none text-zinc-800 text-left">
      <div className="max-w-4xl mx-auto space-y-12">
        
        {/* Banner header */}
        <div className="bg-gradient-to-r from-[#ff3f6c] to-[#ff6b8b] rounded-2xl p-8 sm:p-12 text-white flex flex-col md:flex-row justify-between items-center gap-8 shadow-md">
          <div className="space-y-4 max-w-lg">
            <h1 className="text-3xl sm:text-4xl font-black uppercase tracking-wider">AURA Gift Cards</h1>
            <p className="text-xs sm:text-sm font-semibold opacity-90 leading-relaxed">
              Give the gift of choice. Perfect for birthdays, weddings, anniversaries, or simply saying thank you. Buy cards for friends, family, or check your existing card balance.
            </p>
          </div>
          <div className="relative w-56 h-36 bg-white/10 border border-white/20 rounded-xl backdrop-blur-md shadow-lg p-5 flex flex-col justify-between shrink-0">
            <div className="flex justify-between items-start">
              <span className="text-xs font-black uppercase tracking-widest">AURA CARD</span>
              <CreditCard className="h-6 w-6 opacity-70" />
            </div>
            <div className="text-sm font-extrabold tracking-widest mt-4">**** **** **** 8420</div>
            <div className="flex justify-between items-end text-[10px] uppercase font-bold opacity-80 mt-2">
              <span>Aura Fashion Insider</span>
              <span>Rs. 5,000</span>
            </div>
          </div>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-2 gap-8">
          
          {/* Buy Section */}
          <div className="bg-white border border-zinc-150 rounded-2xl p-6 sm:p-8 shadow-3xs space-y-6">
            <h2 className="text-md font-black uppercase tracking-wider text-zinc-900 border-b border-zinc-100 pb-4">
              Buy A Gift Card
            </h2>
            <p className="text-xs text-zinc-450 font-semibold leading-relaxed">
              Select an amount to purchase a high-fidelity digital gift voucher delivered directly to the recipient's inbox.
            </p>
            <div className="grid grid-cols-2 gap-3">
              {[1000, 2000, 5000, 10000].map((amt) => (
                <button
                  key={amt}
                  onClick={() => handleBuyCard(amt)}
                  className="py-3 px-4 bg-zinc-50 border border-zinc-200 rounded-xl hover:border-[#ff3f6c] hover:bg-[#ff3f6c]/5 font-black text-xs text-zinc-750 transition-all cursor-pointer text-center"
                >
                  Rs. {amt.toLocaleString()}
                </button>
              ))}
            </div>
            <Button onClick={() => handleBuyCard(5000)} className="w-full bg-[#ff3f6c] hover:bg-[#e6355f] text-white font-black text-xs uppercase tracking-widest py-6 rounded-xl shadow-3xs gap-2">
              Custom Amount Card <ArrowRight className="h-4 w-4" />
            </Button>
          </div>

          {/* Balance Checker Section */}
          <div className="bg-white border border-zinc-150 rounded-2xl p-6 sm:p-8 shadow-3xs space-y-6">
            <h2 className="text-md font-black uppercase tracking-wider text-zinc-900 border-b border-zinc-100 pb-4">
              Check Gift Card Balance
            </h2>
            <form onSubmit={handleCheckBalance} className="space-y-4">
              <div className="flex flex-col gap-1.5">
                <label className="text-[10px] font-black uppercase text-zinc-450 tracking-wider">Gift Card Number</label>
                <input 
                  type="text" 
                  value={cardNumber}
                  onChange={(e) => setCardNumber(e.target.value)}
                  placeholder="Enter 16-digit card number"
                  className="w-full text-xs font-semibold px-4 py-3 bg-zinc-50 border border-zinc-200 rounded-xl focus:outline-none focus:border-[#ff3f6c] transition-colors"
                  required
                />
              </div>

              <div className="flex flex-col gap-1.5">
                <label className="text-[10px] font-black uppercase text-zinc-450 tracking-wider">6-Digit Card PIN</label>
                <input 
                  type="password" 
                  value={pin}
                  onChange={(e) => setPin(e.target.value)}
                  placeholder="Enter Card PIN"
                  className="w-full text-xs font-semibold px-4 py-3 bg-zinc-50 border border-zinc-200 rounded-xl focus:outline-none focus:border-[#ff3f6c] transition-colors"
                  required
                />
              </div>

              <button
                type="submit"
                disabled={checking}
                className="w-full py-3.5 bg-zinc-800 hover:bg-zinc-950 text-white text-[10px] font-black uppercase tracking-wider rounded-xl transition-all cursor-pointer shadow-3xs"
              >
                {checking ? "Checking..." : "Verify & Fetch Balance"}
              </button>
            </form>

            {balance !== null && (
              <div className="bg-emerald-50 border border-emerald-200 rounded-xl p-4 text-center space-y-1">
                <p className="text-[10px] font-bold text-emerald-600 uppercase tracking-wider">Current Card Balance</p>
                <h3 className="text-xl font-black text-emerald-800">Rs. {balance.toLocaleString()}.00</h3>
              </div>
            )}
          </div>

        </div>

        {/* FAQs */}
        <div className="bg-white border border-zinc-150 rounded-2xl p-6 sm:p-8 shadow-3xs space-y-6">
          <h2 className="text-md font-black uppercase tracking-wider text-zinc-900 border-b border-zinc-100 pb-4 flex items-center gap-2">
            <HelpCircle className="h-5 w-5 text-zinc-400" /> Frequently Asked Questions
          </h2>
          <div className="divide-y divide-zinc-100">
            <div className="py-4 first:pt-0">
              <h4 className="text-xs font-black text-zinc-800 uppercase tracking-wide">Where can I use my AURA Gift Card?</h4>
              <p className="text-[11px] text-zinc-450 font-semibold leading-relaxed mt-2">
                AURA Gift Cards can be applied directly at the final payment gateway screen during checkout on our online store and all affiliated retail storefronts.
              </p>
            </div>
            <div className="py-4 last:pb-0">
              <h4 className="text-xs font-black text-zinc-800 uppercase tracking-wide">Do AURA Gift Cards expire?</h4>
              <p className="text-[11px] text-zinc-450 font-semibold leading-relaxed mt-2">
                All purchased gift vouchers are valid for up to 12 months from the date of issuance.
              </p>
            </div>
          </div>
        </div>

      </div>
    </div>
  );
}
