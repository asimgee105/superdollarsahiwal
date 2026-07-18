"use client";

import * as React from "react";
import Link from "next/link";
import { GitCompare, ArrowLeft, Trash2 } from "lucide-react";
import { getRelativePath } from "@/lib/utils";

export default function CompareProductsPage() {
  const [items, setItems] = React.useState([
    {
      id: 1,
      title: "AURA Premium Denim Jacket",
      price: "Rs. 4,999",
      brand: "AURA Premium",
      fabric: "Pure Cotton Denim",
      fit: "Oversized Fit",
      wash: "Dry Clean recommended",
      image: "https://images.unsplash.com/photo-1576995853123-5a10305d93c0?q=80&w=400"
    },
    {
      id: 2,
      title: "AURA Smart Fit Oxford Shirt",
      price: "Rs. 2,999",
      brand: "AURA Premium",
      fabric: "Cotton Blend",
      fit: "Smart Fit",
      wash: "Machine wash",
      image: "https://images.unsplash.com/photo-1521572267360-ee0c2909d518?q=80&w=400"
    }
  ]);

  const removeCompare = (id: number) => {
    setItems(items.filter(item => item.id !== id));
  };

  return (
    <main className="min-h-screen bg-zinc-50/50 py-12 px-4 sm:px-6 lg:px-8 font-sans select-none">
      <div className="max-w-4xl mx-auto space-y-6">
        
        {/* Back Link */}
        <Link href={getRelativePath("/")} className="inline-flex items-center gap-1.5 text-[11px] font-black uppercase tracking-wider text-zinc-450 hover:text-zinc-800 transition-colors">
          <ArrowLeft className="h-3.5 w-3.5" /> Back to Storefront
        </Link>

        {/* Content Card */}
        <div className="bg-white border border-zinc-150 rounded-2xl p-6 sm:p-8 shadow-3xs space-y-6">
          <div className="flex items-center gap-3 border-b border-zinc-100 pb-5">
            <div className="p-2.5 bg-[#f51c50]/5 rounded-xl text-[#f51c50]">
              <GitCompare className="h-5 w-5" />
            </div>
            <div>
              <span className="text-[10px] font-black uppercase tracking-widest text-[#f51c50]">Compare Matrix</span>
              <h1 className="text-lg font-black text-zinc-900 uppercase tracking-wider mt-0.5">Product Comparison</h1>
            </div>
          </div>

          {items.length === 0 ? (
            <div className="py-12 text-center text-zinc-500 font-semibold text-sm">
              No products selected to compare. Browse catalog to add items.
            </div>
          ) : (
            <div className="overflow-x-auto">
              <table className="w-full text-left border-collapse text-xs sm:text-sm">
                <thead>
                  <tr className="border-b border-zinc-200">
                    <th className="py-4 font-black uppercase tracking-wider text-[10px] text-zinc-450 w-[200px]">Attribute</th>
                    {items.map(item => (
                      <th key={item.id} className="py-4 px-4">
                        <div className="space-y-3">
                          <img src={item.image} alt={item.title} className="w-24 h-24 object-cover rounded-xl border border-zinc-150" />
                          <div className="flex items-center justify-between gap-2">
                            <span className="font-black text-zinc-850 uppercase tracking-wide line-clamp-1">{item.title}</span>
                            <button onClick={() => removeCompare(item.id)} className="text-zinc-400 hover:text-[#f51c50] cursor-pointer">
                              <Trash2 className="h-4 w-4" />
                            </button>
                          </div>
                        </div>
                      </th>
                    ))}
                  </tr>
                </thead>
                <tbody className="divide-y divide-zinc-100 font-semibold text-zinc-650">
                  <tr>
                    <td className="py-3.5 font-black uppercase text-[10px] text-zinc-400">Price</td>
                    {items.map(item => <td key={item.id} className="py-3.5 px-4 text-[#f51c50] font-black">{item.price}</td>)}
                  </tr>
                  <tr>
                    <td className="py-3.5 font-black uppercase text-[10px] text-zinc-400">Brand</td>
                    {items.map(item => <td key={item.id} className="py-3.5 px-4">{item.brand}</td>)}
                  </tr>
                  <tr>
                    <td className="py-3.5 font-black uppercase text-[10px] text-zinc-400">Fabric Composition</td>
                    {items.map(item => <td key={item.id} className="py-3.5 px-4">{item.fabric}</td>)}
                  </tr>
                  <tr>
                    <td className="py-3.5 font-black uppercase text-[10px] text-zinc-400">Styling & Fit</td>
                    {items.map(item => <td key={item.id} className="py-3.5 px-4">{item.fit}</td>)}
                  </tr>
                  <tr>
                    <td className="py-3.5 font-black uppercase text-[10px] text-zinc-400">Care Instructions</td>
                    {items.map(item => <td key={item.id} className="py-3.5 px-4">{item.wash}</td>)}
                  </tr>
                </tbody>
              </table>
            </div>
          )}
        </div>
      </div>
    </main>
  );
}
