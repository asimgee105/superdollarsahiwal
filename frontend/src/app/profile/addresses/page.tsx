"use client";

import * as React from "react";
import { MapPin, Plus, Trash2, Home, Briefcase } from "lucide-react";
import { toast } from "sonner";

export default function ProfileAddressesPage() {
  const [addresses, setAddresses] = React.useState([
    { id: 1, name: "Asim Gee", phone: "03001234567", type: "Home", street: "House 12, Block J3", city: "Johar Town", state: "Punjab", zip: "54782" },
    { id: 2, name: "Asim Gee", phone: "03010000001", type: "Office", street: "Floor 4, Software Park", city: "Gulberg III, Lahore", state: "Punjab", zip: "54660" },
  ]);

  const handleDelete = (id: number, type: string) => {
    setAddresses((prev) => prev.filter((a) => a.id !== id));
    toast.info(`${type} Address deleted successfully.`);
  };

  const handleAddAddress = () => {
    toast.success("Opening add address form modal.");
  };

  return (
    <div className="text-zinc-800 text-left space-y-6">
      
      {/* Page Header */}
      <div className="flex items-center justify-between pb-4 border-b border-zinc-100">
        <div className="flex items-center gap-3">
          <MapPin className="h-6 w-6 text-[#ff3f6c]" />
          <h1 className="text-lg font-black uppercase tracking-wider text-zinc-950">
            Saved Delivery Addresses
          </h1>
        </div>
        <button
          onClick={handleAddAddress}
          className="flex items-center gap-1 text-[10px] font-black uppercase tracking-wider text-[#ff3f6c] hover:underline cursor-pointer"
        >
          <Plus className="h-4 w-4" /> Add New Address
        </button>
      </div>

      {addresses.length === 0 ? (
        <div className="text-center py-16 bg-zinc-50/50 border border-zinc-150 rounded-2xl p-8 max-w-lg mx-auto shadow-3xs select-none">
          <div className="h-16 w-16 bg-[#ff3f6c]/5 rounded-full flex items-center justify-center mb-6 mx-auto">
            <MapPin className="h-8 w-8 text-[#ff3f6c]" />
          </div>
          <h2 className="text-sm font-black uppercase tracking-wider text-zinc-900">No Saved Addresses</h2>
          <p className="text-[11px] text-zinc-450 font-semibold text-center mt-2 max-w-xs leading-relaxed mx-auto">
            You don't have any saved delivery addresses. Add one to speed up checkout!
          </p>
        </div>
      ) : (
        <div className="grid grid-cols-1 sm:grid-cols-2 gap-6">
          {addresses.map((address) => (
            <div key={address.id} className="border border-zinc-150 rounded-xl p-5 shadow-3xs bg-white flex flex-col justify-between min-h-[160px] relative hover:border-zinc-300 transition-all">
              
              <div className="space-y-2">
                <div className="flex justify-between items-center">
                  <span className="inline-flex items-center gap-1 px-2.5 py-0.5 bg-zinc-50 border border-zinc-150 text-[9px] font-black uppercase tracking-wider rounded-sm text-zinc-650">
                    {address.type === "Home" ? <Home className="h-3 w-3 text-zinc-450" /> : <Briefcase className="h-3 w-3 text-zinc-450" />}
                    {address.type}
                  </span>
                  
                  <button
                    onClick={() => handleDelete(address.id, address.type)}
                    className="text-zinc-400 hover:text-red-500 transition-colors p-1.5 rounded-lg hover:bg-zinc-50 cursor-pointer"
                  >
                    <Trash2 className="h-4 w-4" />
                  </button>
                </div>

                <div className="text-xs">
                  <h4 className="font-extrabold text-zinc-800">{address.name}</h4>
                  <p className="text-zinc-500 mt-1 font-semibold leading-relaxed">{address.street}, {address.city}</p>
                  <p className="text-zinc-450 font-semibold mt-0.5">{address.state} - {address.zip}</p>
                </div>
              </div>

              <div className="border-t border-zinc-100 pt-3 mt-3 text-[10px] font-bold text-zinc-400 uppercase tracking-wide">
                Phone: <span className="text-zinc-700 font-extrabold">{address.phone}</span>
              </div>

            </div>
          ))}
        </div>
      )}

    </div>
  );
}
