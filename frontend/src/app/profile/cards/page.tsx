"use client";

import * as React from "react";
import { CreditCard, Plus, Trash2 } from "lucide-react";
import { toast } from "sonner";
import { Button } from "@/components/ui/button";

export default function ProfileCardsPage() {
  const [cards, setCards] = React.useState([
    { id: 1, name: "ASIM GEE", number: "**** **** **** 4321", expiry: "12/29", type: "Visa" },
    { id: 2, name: "ASIM GEE", number: "**** **** **** 9876", expiry: "08/28", type: "Mastercard" },
  ]);

  const handleDelete = (id: number, number: string) => {
    setCards((prev) => prev.filter((c) => c.id !== id));
    toast.info(`Card ending in ${number.split(" ").pop()} deleted successfully.`);
  };

  const handleAddCard = () => {
    toast.success("Redirecting to secure gateway to add card.");
  };

  return (
    <div className="text-zinc-800 text-left space-y-6">
      
      {/* Page Header */}
      <div className="flex items-center justify-between pb-4 border-b border-zinc-100">
        <div className="flex items-center gap-3">
          <CreditCard className="h-6 w-6 text-[#ff3f6c]" />
          <h1 className="text-lg font-black uppercase tracking-wider text-zinc-950">
            Saved Credit & Debit Cards
          </h1>
        </div>
        <button
          onClick={handleAddCard}
          className="flex items-center gap-1 text-[10px] font-black uppercase tracking-wider text-[#ff3f6c] hover:underline cursor-pointer"
        >
          <Plus className="h-4 w-4" /> Add New Card
        </button>
      </div>

      {cards.length === 0 ? (
        <div className="text-center py-16 bg-zinc-50/50 border border-zinc-150 rounded-2xl p-8 max-w-lg mx-auto shadow-3xs select-none">
          <div className="h-16 w-16 bg-[#ff3f6c]/5 rounded-full flex items-center justify-center mb-6 mx-auto">
            <CreditCard className="h-8 w-8 text-[#ff3f6c]" />
          </div>
          <h2 className="text-sm font-black uppercase tracking-wider text-zinc-900">No Saved Cards</h2>
          <p className="text-[11px] text-zinc-450 font-semibold text-center mt-2 max-w-xs leading-relaxed mx-auto">
            Save your payment cards during checkout for faster purchases next time.
          </p>
        </div>
      ) : (
        <div className="grid grid-cols-1 sm:grid-cols-2 gap-6">
          {cards.map((card) => (
            <div key={card.id} className="border border-zinc-150 rounded-xl p-5 shadow-3xs bg-zinc-900 text-white flex flex-col justify-between min-h-[150px] relative overflow-hidden">
              <div className="absolute top-0 right-0 w-24 h-24 bg-white/5 rounded-full blur-xl pointer-events-none"></div>
              
              <div className="flex justify-between items-start">
                <span className="text-[10px] uppercase font-black tracking-widest text-[#ff3f6c]">{card.type}</span>
                <button
                  onClick={() => handleDelete(card.id, card.number)}
                  className="text-zinc-500 hover:text-red-500 transition-colors p-1.5 rounded-lg hover:bg-white/5"
                >
                  <Trash2 className="h-4 w-4" />
                </button>
              </div>

              <div className="text-sm font-bold tracking-widest my-4">{card.number}</div>

              <div className="flex justify-between text-[9px] uppercase font-bold tracking-wider text-zinc-400">
                <div>
                  <p className="opacity-60 text-[7px] mb-0.5">Card Holder</p>
                  <span>{card.name}</span>
                </div>
                <div>
                  <p className="opacity-60 text-[7px] mb-0.5">Expires</p>
                  <span>{card.expiry}</span>
                </div>
              </div>

            </div>
          ))}
        </div>
      )}

    </div>
  );
}
