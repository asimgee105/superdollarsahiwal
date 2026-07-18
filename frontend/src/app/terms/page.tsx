"use client";

import * as React from "react";
import Link from "next/link";
import { Scale, ArrowLeft } from "lucide-react";
import { getRelativePath } from "@/lib/utils";

export default function TermsConditionsPage() {
  const [data, setData] = React.useState({ title: "Terms & Conditions", content: "" });
  const [loading, setLoading] = React.useState(true);

  const API_URL = process.env.NEXT_PUBLIC_API_URL || "http://localhost:8000";

  React.useEffect(() => {
    fetch(`${API_URL}/api/v1/page/terms-conditions?nocache=1`)
      .then((res) => res.json())
      .then((resData) => {
        if (resData) setData(resData);
        setLoading(false);
      })
      .catch(() => setLoading(false));
  }, [API_URL]);

  function parseMarkdown(text: string) {
    if (!text) return null;
    return text.split("\n").map((line, idx) => {
      if (line.startsWith("# ")) {
        return <h1 key={idx} className="text-2xl font-black text-zinc-900 uppercase tracking-widest mt-8 mb-4">{line.replace("# ", "")}</h1>;
      }
      if (line.startsWith("## ")) {
        return <h2 key={idx} className="text-base font-black text-zinc-800 uppercase tracking-wider mt-6 mb-3">{line.replace("## ", "")}</h2>;
      }
      if (line.startsWith("- ")) {
        return <li key={idx} className="list-disc pl-4 ml-4 text-zinc-650 font-semibold mb-1.5 leading-relaxed">{line.replace("- ", "")}</li>;
      }
      if (line.trim() === "") {
        return <div key={idx} className="h-2"></div>;
      }
      return <p key={idx} className="text-zinc-650 font-medium leading-relaxed mb-3.5 text-xs sm:text-sm">{line}</p>;
    });
  }

  return (
    <main className="min-h-screen bg-zinc-50/50 py-12 px-4 sm:px-6 lg:px-8 font-sans select-none">
      <div className="max-w-3xl mx-auto space-y-6">
        
        {/* Back Link */}
        <Link href={getRelativePath("/")} className="inline-flex items-center gap-1.5 text-[11px] font-black uppercase tracking-wider text-zinc-450 hover:text-zinc-800 transition-colors">
          <ArrowLeft className="h-3.5 w-3.5" /> Back to Storefront
        </Link>

        {/* Content Card */}
        <div className="bg-white border border-zinc-150 rounded-2xl p-6 sm:p-8 shadow-3xs space-y-6">
          <div className="flex items-center gap-3 border-b border-zinc-100 pb-5">
            <div className="p-2.5 bg-[#f51c50]/5 rounded-xl text-[#f51c50]">
              <Scale className="h-5 w-5" />
            </div>
            <div>
              <span className="text-[10px] font-black uppercase tracking-widest text-[#f51c50]">Legal Policy</span>
              <h1 className="text-lg font-black text-zinc-900 uppercase tracking-wider mt-0.5">{data.title}</h1>
            </div>
          </div>

          <div className="prose max-w-none">
            {loading ? (
              <div className="space-y-4 py-8">
                <div className="h-4 bg-zinc-100 rounded-sm w-3/4 animate-pulse"></div>
                <div className="h-4 bg-zinc-100 rounded-sm w-full animate-pulse"></div>
                <div className="h-4 bg-zinc-100 rounded-sm w-5/6 animate-pulse"></div>
              </div>
            ) : (
              parseMarkdown(data.content)
            )}
          </div>
        </div>
      </div>
    </main>
  );
}
