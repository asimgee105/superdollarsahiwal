"use client";

import * as React from "react";
import { useSearchParams } from "next/navigation";
import { ProductDetails } from "@/components/shared/product-details";

function ProductPageContent() {
  const searchParams = useSearchParams();
  const id = searchParams.get("id") || "";

  if (!id) {
    return (
      <div className="text-center py-20 text-xs font-black uppercase tracking-widest text-[#f51c50]">
        No Product Selected.
      </div>
    );
  }

  return <ProductDetails id={id} />;
}

export default function ProductPage() {
  return (
    <React.Suspense fallback={<div className="text-center py-20 text-xs font-black uppercase tracking-widest text-[#f51c50]">Loading Product...</div>}>
      <ProductPageContent />
    </React.Suspense>
  );
}
