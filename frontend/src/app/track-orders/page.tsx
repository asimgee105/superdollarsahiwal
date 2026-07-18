"use client";

import * as React from "react";
import { useRouter } from "next/navigation";
import { getRelativePath } from "@/lib/utils";

export default function TrackOrdersRedirectPage() {
  const router = useRouter();

  React.useEffect(() => {
    router.replace(getRelativePath("/track"));
  }, [router]);

  return (
    <div className="min-h-screen bg-zinc-50 flex items-center justify-center font-sans">
      <div className="text-center space-y-3">
        <div className="h-6 w-6 border-2 border-t-zinc-900 border-zinc-200 rounded-full animate-spin mx-auto"></div>
        <p className="text-[11px] font-black uppercase tracking-wider text-zinc-450">Redirecting to Track Orders...</p>
      </div>
    </div>
  );
}
