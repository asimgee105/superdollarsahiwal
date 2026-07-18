"use client";

import * as React from "react";
import { useSearchParams } from "next/navigation";
import Image from "next/image";
import { 
  Search, 
  MapPin, 
  Truck, 
  Calendar, 
  Package, 
  CheckCircle2, 
  Clock, 
  ChevronRight, 
  RotateCcw,
  Sparkles
} from "lucide-react";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { toast } from "sonner";
import Link from "next/link";
import { getRelativePath } from "@/lib/utils";

// Simulated tracking logs generator based on the code to make any code work realistically
function getSimulatedTrackingInfo(code: string) {
  // Check if we have session stored order details
  if (typeof window !== "undefined") {
    const saved = sessionStorage.getItem(code);
    if (saved) {
      try {
        const order = JSON.parse(saved);
        return {
          exists: true,
          fullName: order.fullName,
          address: order.address,
          total: order.total,
          items: order.items,
          status: "In Transit",
          date: order.date
        };
      } catch (e) {}
    }
  }

  // Fallback default simulation for testing random codes
  return {
    exists: false,
    fullName: "Valued Customer",
    address: "Main Boulevard Colony, Gulberg III, Lahore, Pakistan",
    total: 2499,
    items: [{ title: "AURA Signature Embroidered Suit", quantity: 1, price: 2499, brand: "AURA PREMIUM" }],
    status: "In Transit",
    date: new Date().toLocaleDateString()
  };
}

function TrackOrderContent() {
  const searchParams = useSearchParams();
  const initialCode = searchParams.get("code") || "";
  
  const [trackingCode, setTrackingCode] = React.useState(initialCode);
  const [activeCode, setActiveCode] = React.useState(initialCode);
  const [loading, setLoading] = React.useState(false);

  const handleSearch = (e: React.FormEvent) => {
    e.preventDefault();
    if (!trackingCode.trim()) {
      toast.warning("Please enter a tracking number.");
      return;
    }
    setLoading(true);
    setTimeout(() => {
      setActiveCode(trackingCode.trim());
      setLoading(false);
      toast.success("Shipment data retrieved.");
    }, 600);
  };

  const orderInfo = activeCode ? getSimulatedTrackingInfo(activeCode) : null;

  return (
    <div className="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8 bg-[#fafafa] min-h-screen text-zinc-800 text-left">
      
      {/* Page Header */}
      <div className="flex flex-col gap-2 mb-10 text-center sm:text-left">
        <span className="text-[10px] font-black uppercase tracking-widest text-[#f51c50]">Live Logistics Center</span>
        <h1 className="text-2xl sm:text-3xl font-black uppercase tracking-wider text-zinc-950">
          Track Your Package
        </h1>
        <p className="text-xs text-zinc-450 font-bold max-w-md">
          Enter your unique tracking code below to view real-time location logs and delivery timeline status.
        </p>
      </div>

      {/* Code Input Search Box */}
      <div className="max-w-2xl bg-white border border-zinc-150 rounded-2xl p-6 shadow-3xs mb-8">
        <form onSubmit={handleSearch} className="flex flex-col sm:flex-row gap-3">
          <div className="relative flex-grow">
            <Search className="absolute left-4 top-3.5 h-4.5 w-4.5 text-zinc-400" />
            <Input 
              type="text" 
              placeholder="Enter Tracking Number (e.g. AURA-TRK-74839219)" 
              value={trackingCode}
              onChange={(e) => setTrackingCode(e.target.value)}
              className="text-xs rounded-xl py-6 pl-11 border-zinc-200 focus-visible:ring-[#f51c50] shadow-3xs uppercase placeholder:normal-case font-bold"
            />
          </div>
          <Button 
            type="submit" 
            disabled={loading}
            className="bg-[#f51c50] hover:bg-[#f51c50]/90 text-white font-black text-xs uppercase tracking-widest px-8 py-6 rounded-xl shadow-xs transition-all disabled:opacity-50"
          >
            {loading ? "Searching..." : "Track Package"}
          </Button>
        </form>
      </div>

      {/* Timeline Tracking results */}
      {activeCode && orderInfo ? (
        <div className="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
          
          {/* Left Column: Shipment Status Timeline Details */}
          <div className="lg:col-span-8 space-y-6">
            
            {/* Header info badge */}
            <div className="bg-white border border-zinc-150 rounded-2xl p-5 sm:p-6 shadow-2xs flex flex-col sm:flex-row justify-between sm:items-center gap-4">
              <div className="space-y-1">
                <span className="text-[9px] font-black uppercase text-zinc-400 tracking-wider">Tracking Code</span>
                <h2 className="text-sm font-black text-zinc-900">{activeCode}</h2>
              </div>
              <div className="flex items-center gap-2 bg-[#f51c50]/5 text-[#f51c50] px-4 py-2 rounded-full text-xs font-black select-none">
                <Truck className="h-4 w-4" /> In Transit
              </div>
              <div className="space-y-1">
                <span className="text-[9px] font-black uppercase text-zinc-400 tracking-wider">Expected Delivery</span>
                <div className="flex items-center gap-1 text-xs font-black text-zinc-900">
                  <Calendar className="h-4 w-4 text-[#f51c50]" /> 3 - 5 Business Days
                </div>
              </div>
            </div>

            {/* Vertical Delivery Logs list */}
            <div className="bg-white border border-zinc-150 rounded-2xl p-6 shadow-2xs space-y-6 relative overflow-hidden">
              <div className="absolute top-6 bottom-6 left-[21px] w-0.5 bg-zinc-100 z-0"></div>
              
              <h3 className="text-xs font-black uppercase tracking-widest text-zinc-900 border-b border-zinc-100 pb-4 mb-4 select-none flex items-center gap-2">
                <Package className="h-4.5 w-4.5 text-[#f51c50]" /> Shipment Activity Logs
              </h3>

              {/* Status Nodes */}
              {[
                {
                  time: "03:45 PM Today",
                  title: "In Transit",
                  desc: "Shipment departed from Lahore Logistics Hub outbound to Destination Hub.",
                  active: true,
                  completed: true
                },
                {
                  time: "09:30 AM Today",
                  title: "Handed Over to Carrier",
                  desc: "Package sorted and assigned to AURA Logistics Carrier Express.",
                  active: false,
                  completed: true
                },
                {
                  time: "05:15 PM Yesterday",
                  title: "Packed & Sealed",
                  desc: "Item packed, barcoded and verified at Lahore Packaging Unit.",
                  active: false,
                  completed: true
                },
                {
                  time: "02:40 PM Yesterday",
                  title: "Order Placed",
                  desc: "Order confirmed, transaction successfully verified, invoice generated.",
                  active: false,
                  completed: true
                }
              ].map((log, idx) => (
                <div key={idx} className="flex gap-4 relative z-10 text-left">
                  <div className={`h-11 w-11 rounded-full flex items-center justify-center border-2 flex-none ${
                    log.active 
                      ? "bg-[#f51c50] border-[#f51c50] text-white shadow-md shadow-[#f51c50]/20" 
                      : log.completed
                        ? "bg-emerald-500 border-emerald-500 text-white"
                        : "bg-white border-zinc-200 text-zinc-400"
                  }`}>
                    {log.active ? <Clock className="h-5 w-5" /> : <CheckCircle2 className="h-5 w-5" />}
                  </div>
                  <div className="space-y-1 pt-1">
                    <span className="text-[10px] text-zinc-400 font-bold">{log.time}</span>
                    <h4 className="text-xs font-black uppercase tracking-wider text-zinc-900">{log.title}</h4>
                    <p className="text-xs text-zinc-450 leading-relaxed font-semibold max-w-lg">{log.desc}</p>
                  </div>
                </div>
              ))}
            </div>

          </div>

          {/* Right Column: Order Details Summary */}
          <div className="lg:col-span-4 space-y-6">
            
            {/* Delivery address details summary */}
            <div className="bg-white border border-zinc-150 rounded-2xl p-5 shadow-2xs space-y-4">
              <h3 className="text-[10px] font-black uppercase tracking-widest text-zinc-400 select-none border-b border-zinc-100 pb-3 mb-1">
                Shipping Details
              </h3>
              <div>
                <span className="text-[9.5px] font-black text-zinc-400 uppercase tracking-wider block">Customer Name</span>
                <span className="text-xs font-extrabold text-zinc-800 mt-0.5 block">{orderInfo.fullName}</span>
              </div>
              <div className="pt-2 border-t border-zinc-50">
                <span className="text-[9.5px] font-black text-zinc-400 uppercase tracking-wider block">Street Address</span>
                <span className="text-xs font-extrabold text-zinc-855 mt-0.5 block leading-relaxed flex items-start gap-1">
                  <MapPin className="h-4 w-4 text-zinc-450 mt-0.5 flex-none" /> {orderInfo.address}
                </span>
              </div>
            </div>

            {/* Order Items summary card */}
            <div className="bg-white border border-zinc-150 rounded-2xl p-5 shadow-2xs space-y-4">
              <h3 className="text-[10px] font-black uppercase tracking-widest text-zinc-400 select-none border-b border-zinc-100 pb-3 mb-1">
                Order Items Summary
              </h3>
              
              <div className="divide-y divide-zinc-100">
                {orderInfo.items.map((item: any, idx: number) => (
                  <div key={idx} className="flex gap-3 py-3 first:pt-0 last:pb-0">
                    {item.image && (
                      <div className="relative w-12 aspect-[3/4] bg-zinc-50 rounded-lg overflow-hidden border border-zinc-100">
                        <Image src={item.image} alt={item.title} fill sizes="48px" className="object-cover" />
                      </div>
                    )}
                    <div className="flex-grow pt-0.5 text-xs text-left">
                      <span className="text-[8.5px] font-black text-[#f51c50] uppercase block">{item.brand || "AURA"}</span>
                      <span className="font-bold text-zinc-800 line-clamp-1 mt-0.5">{item.title}</span>
                      <span className="text-[10px] text-zinc-400 font-extrabold mt-1 block">
                        Qty: {item.quantity} • Rs. {item.price}
                      </span>
                    </div>
                  </div>
                ))}
              </div>

              <div className="pt-4 border-t border-zinc-150 flex justify-between items-baseline select-none">
                <span className="text-[10px] font-black uppercase tracking-wider text-zinc-400">Total Paid</span>
                <span className="text-sm font-black text-zinc-950">Rs. {orderInfo.total}</span>
              </div>
            </div>

            {/* Help Support info box */}
            <div className="bg-[#f51c50]/5 border border-[#f51c50]/10 rounded-2xl p-5 shadow-3xs text-center select-none space-y-3">
              <Sparkles className="h-5 w-5 text-[#f51c50] mx-auto" />
              <h4 className="text-xs font-black uppercase tracking-wide text-zinc-900">Need Help with Order?</h4>
              <p className="text-[10.5px] text-zinc-450 leading-relaxed font-semibold">
                If you have queries regarding package delivery or sizing issues, reach support center anytime.
              </p>
              <Link href={getRelativePath("/")} className="block w-full">
                <Button variant="ghost" className="w-full text-xs font-black uppercase text-[#f51c50] hover:bg-[#f51c50]/5">
                  Contact Support Center
                </Button>
              </Link>
            </div>

          </div>

        </div>
      ) : (
        /* Prompt to input code if none is active */
        <div className="flex flex-col items-center justify-center py-20 bg-white border border-zinc-150 rounded-2xl p-8 max-w-lg mx-auto shadow-xs select-none">
          <div className="h-16 w-16 bg-[#f51c50]/5 rounded-full flex items-center justify-center mb-6">
            <Search className="h-8 w-8 text-[#f51c50]" />
          </div>
          <h2 className="text-lg font-black uppercase tracking-wider text-zinc-900">No Tracking Active</h2>
          <p className="text-xs text-zinc-450 font-semibold text-center mt-2 max-w-xs leading-relaxed">
            Please enter your 12-digit package tracking code above to search dispatch and location details.
          </p>
        </div>
      )}

    </div>
  );
}

export default function TrackOrderPage() {
  return (
    <React.Suspense fallback={
      <div className="flex items-center justify-center min-h-screen bg-[#fafafa]">
        <div className="animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-[#f51c50]"></div>
      </div>
    }>
      <TrackOrderContent />
    </React.Suspense>
  );
}
