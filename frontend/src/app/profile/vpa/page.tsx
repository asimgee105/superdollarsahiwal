"use client";

import * as React from "react";
import { CreditCard, Plus, Trash2 } from "lucide-react";
import { toast } from "sonner";

export default function ProfileVpaPage() {
  const [vpas, setVpas] = React.useState([
    { id: 1, vpa: "asimgee105@ybl", provider: "UPI / PhonePe" },
    { id: 2, vpa: "asimgee@paytm", provider: "UPI / Paytm" },
  ]);

  const handleDelete = (id: number, vpa: string) => {
    setVpas((prev) => prev.filter((v) => v.id !== id));
    toast.info(`UPI VPA ${vpa} deleted successfully.`);
  };

  const handleAddVpa = () => {
    toast.success("Redirecting to verify and save UPI VPA.");
  };

  return (
    <div className="text-zinc-800 text-left space-y-6">
      
      {/* Page Header */}
      <div className="flex items-center justify-between pb-4 border-b border-zinc-100">
        <div className="flex items-center gap-3">
          <CreditCard className="h-6 w-6 text-[#ff3f6c]" />
          <h1 className="text-lg font-black uppercase tracking-wider text-zinc-950">
            Saved UPI VPA Accounts
          </h1>
        </div>
        <button
          onClick={handleAddVpa}
          className="flex items-center gap-1 text-[10px] font-black uppercase tracking-wider text-[#ff3f6c] hover:underline cursor-pointer"
        >
          <Plus className="h-4 w-4" /> Add New VPA
        </button>
      </div>

      {vpas.length === 0 ? (
        <div className="text-center py-16 bg-zinc-50/50 border border-zinc-150 rounded-2xl p-8 max-w-lg mx-auto shadow-3xs select-none">
          <div className="h-16 w-16 bg-[#ff3f6c]/5 rounded-full flex items-center justify-center mb-6 mx-auto">
            <CreditCard className="h-8 w-8 text-[#ff3f6c]" />
          </div>
          <h2 className="text-sm font-black uppercase tracking-wider text-zinc-900">No Saved VPAs</h2>
          <p className="text-[11px] text-zinc-450 font-semibold text-center mt-2 max-w-xs leading-relaxed mx-auto">
            Save your Virtual Payment Addresses (VPA) for quick UPI checkouts.
          </p>
        </div>
      ) : (
        <div className="space-y-4">
          {vpas.map((item) => (
            <div key={item.id} className="border border-zinc-150 rounded-xl p-4 shadow-3xs bg-white flex justify-between items-center hover:border-zinc-300 transition-all">
              
              <div className="flex gap-3.5 items-center">
                <div className="p-2.5 bg-[#ff3f6c]/5 text-[#ff3f6c] rounded-lg">
                  <CreditCard className="h-5 w-5" />
                </div>
                <div>
                  <h4 className="text-xs font-black uppercase text-zinc-850 tracking-wider">{item.vpa}</h4>
                  <p className="text-[9px] font-bold text-zinc-400 uppercase mt-0.5">{item.provider}</p>
                </div>
              </div>

              <button
                onClick={() => handleDelete(item.id, item.vpa)}
                className="text-zinc-400 hover:text-red-500 transition-colors p-2 rounded-lg hover:bg-zinc-50 cursor-pointer"
              >
                <Trash2 className="h-4 w-4" />
              </button>

            </div>
          ))}
        </div>
      )}

    </div>
  );
}
