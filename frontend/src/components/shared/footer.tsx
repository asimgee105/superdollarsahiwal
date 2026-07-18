"use client";

import * as React from "react";
import Link from "next/link";

export function Footer() {
  const [footerData, setFooterData] = React.useState({
    copyright: "© 2026 AURA Commerce. All Rights Reserved.",
    about_text: "AURA is a premium high-fidelity enterprise eCommerce suite.",
    popular_searches: "Makeup | Dresses For Girls | T-Shirts | Sandals | Bags | Sport Shoes",
    col1_title: "ONLINE SHOPPING",
    col1_links: "Men\nWomen\nKids\nHome & Living\nBeauty\nGenz",
    col2_title: "CUSTOMER POLICIES",
    col2_links: "Contact Us\nFAQ\nT&C\nTrack Orders\nShipping\nPrivacy Policy",
    contact: {
      phone: "0300-1234567",
      email: "support@aura.com",
      address: "Gulberg, Lahore, Pakistan"
    },
    socials: {
      facebook: "#",
      twitter: "#",
      youtube: "#",
      instagram: "#"
    }
  });

  const API_URL = process.env.NEXT_PUBLIC_API_URL || "http://localhost:8000";

  React.useEffect(() => {
    fetch(`${API_URL}/api/v1/footer`)
      .then((res) => res.json())
      .then((data) => {
        if (data) {
          setFooterData(prev => ({
            ...prev,
            ...data
          }));
        }
      })
      .catch(() => {});
  }, [API_URL]);

  const col1Items = footerData.col1_links ? footerData.col1_links.split("\n").filter(Boolean) : [];
  const col2Items = footerData.col2_links ? footerData.col2_links.split("\n").filter(Boolean) : [];

  return (
    <footer className="bg-[#fafbfc] border-t border-zinc-200 text-zinc-600 font-sans text-xs pt-12 pb-16 px-4 md:px-8 select-none">
      <div className="mx-auto max-w-7xl w-full flex flex-col gap-10">
        
        {/* Row 1: Directories and Guarantees */}
        <div className="grid grid-cols-1 md:grid-cols-4 gap-8 md:gap-4 pb-8 border-b border-zinc-200">
          
          {/* Col 1: Shopping Links */}
          <div className="flex flex-col gap-6">
            <div>
              <h4 className="text-zinc-800 font-black uppercase tracking-wider mb-3 text-[11px]">
                {footerData.col1_title}
              </h4>
              <ul className="space-y-1.5 font-semibold text-zinc-500">
                {col1Items.map((item, idx) => (
                  <li key={idx}>
                    <Link href={`/catalog?category=${item.toLowerCase().replace(/ /g, "-")}`} className="hover:text-zinc-800 transition-colors">
                      {item}
                    </Link>
                  </li>
                ))}
              </ul>
            </div>
          </div>

          {/* Col 2: Customer Policies */}
          <div>
            <h4 className="text-zinc-800 font-black uppercase tracking-wider mb-3 text-[11px]">
              {footerData.col2_title}
            </h4>
            <ul className="space-y-1.5 font-semibold text-zinc-500">
              {col2Items.map((item, idx) => (
                <li key={idx}>
                  <Link href={`/${item.toLowerCase().replace(/[^a-z0-9]+/g, "-")}`} className="hover:text-zinc-800 transition-colors">
                    {item}
                  </Link>
                </li>
              ))}
            </ul>
          </div>

          {/* Col 3: App Store Badges & Social */}
          <div className="flex flex-col gap-6">
            <div>
              <h4 className="text-zinc-800 font-black uppercase tracking-wider mb-3 text-[11px]">
                EXPERIENCE MYNTRA APP ON MOBILE
              </h4>
              <div className="flex gap-2.5 mt-2">
                <a href="#" className="hover:opacity-90 transition-opacity">
                  <div className="bg-black text-white rounded-md px-3 py-1.5 flex items-center gap-1.5 border border-zinc-800 w-[125px]">
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor">
                      <path d="M5,3 L19,12 L5,21 Z" />
                    </svg>
                    <div className="flex flex-col text-left">
                      <span className="text-[7px] text-zinc-400 leading-none">GET IT ON</span>
                      <span className="text-[10px] font-black leading-none mt-1">Google Play</span>
                    </div>
                  </div>
                </a>
                
                <a href="#" className="hover:opacity-90 transition-opacity">
                  <div className="bg-black text-white rounded-md px-3 py-1.5 flex items-center gap-1.5 border border-zinc-800 w-[125px]">
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor">
                      <path d="M12,2 C6.47,2 2,6.47 2,12 C2,17.53 6.47,22 12,22 C17.53,22 22,17.53 22,12" />
                    </svg>
                    <div className="flex flex-col text-left">
                      <span className="text-[7px] text-zinc-400 leading-none">Download on the</span>
                      <span className="text-[10px] font-black leading-none mt-1">App Store</span>
                    </div>
                  </div>
                </a>
              </div>
            </div>

            <div>
              <h4 className="text-zinc-800 font-black uppercase tracking-wider mb-3 text-[11px]">
                KEEP IN TOUCH
              </h4>
              <div className="flex gap-4">
                <a href={footerData.socials.facebook} target="_blank" rel="noopener noreferrer" className="text-zinc-400 hover:text-[#3b5998] transition-colors">
                  <svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor">
                    <path d="M9 8h-3v4h3v12h5v-12h3.642l.358-4h-4v-1.667c0-.955.192-1.333 1.115-1.333h2.885v-5h-3.808c-3.596 0-5.192 1.583-5.192 4.615v3.385z"/>
                  </svg>
                </a>
                <a href={footerData.socials.twitter} target="_blank" rel="noopener noreferrer" className="text-zinc-400 hover:text-[#1da1f2] transition-colors">
                  <svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor">
                    <path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/>
                  </svg>
                </a>
                <a href={footerData.socials.youtube} target="_blank" rel="noopener noreferrer" className="text-zinc-400 hover:text-[#ff0000] transition-colors">
                  <svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor">
                    <path d="M19.615 3.184c-3.604-.246-11.631-.245-15.23 0-3.897.266-4.356 2.62-4.385 8.816.029 6.185.484 8.549 4.385 8.816 3.6.245 11.626.246 15.23 0 3.897-.266 4.356-2.62 4.385-8.816-.029-6.185-.484-8.549-4.385-8.816zm-10.615 12.816v-8l8 4-8 4z"/>
                  </svg>
                </a>
                <a href={footerData.socials.instagram} target="_blank" rel="noopener noreferrer" className="text-zinc-400 hover:text-[#e1306c] transition-colors">
                  <svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor">
                    <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204 0.013-3.583 0.07-4.849 0.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259 0.014 3.668 0.072 4.948 0.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                  </svg>
                </a>
              </div>
            </div>
          </div>

          {/* Col 4: Guarantees */}
          <div className="flex flex-col gap-6">
            <div className="flex items-start gap-4">
              <div className="p-2 border border-zinc-200 rounded-sm">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#2563eb" strokeWidth="2">
                  <circle cx="12" cy="12" r="10"/>
                  <path d="M12 6v6l4 2"/>
                </svg>
              </div>
              <div className="flex flex-col text-left">
                <span className="text-zinc-800 font-extrabold text-[11px] leading-tight">100% ORIGINAL guarantee</span>
                <span className="text-zinc-400 font-semibold mt-1">for all products</span>
              </div>
            </div>

            <div className="flex items-start gap-4">
              <div className="p-2 border border-zinc-200 rounded-sm">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#2563eb" strokeWidth="2">
                  <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                  <line x1="16" y1="2" x2="16" y2="6"/>
                  <line x1="8" y1="2" x2="8" y2="6"/>
                  <line x1="3" y1="10" x2="21" y2="10"/>
                </svg>
              </div>
              <div className="flex flex-col text-left">
                <span className="text-zinc-800 font-extrabold text-[11px] leading-tight">Return within 14 days</span>
                <span className="text-zinc-400 font-semibold mt-1">of receiving your order</span>
              </div>
            </div>
          </div>

        </div>

        {/* Row 2: Popular Searches */}
        {footerData.popular_searches && (
          <div className="flex flex-col gap-2 pb-6 border-b border-zinc-200">
            <h4 className="text-zinc-800 font-black uppercase text-[11px]">
              POPULAR SEARCHES
            </h4>
            <p className="text-[11px] text-zinc-400 leading-relaxed text-left font-semibold">
              {footerData.popular_searches}
            </p>
          </div>
        )}

        {/* Row 3: Office Address & Contact details */}
        <div className="grid grid-cols-1 md:grid-cols-3 gap-6 pb-6 border-b border-zinc-200">
          <div className="md:col-span-2 text-left">
            <h4 className="text-zinc-800 font-black uppercase text-[11px] mb-2">
              Registered Office Address
            </h4>
            <p className="text-[11px] text-zinc-400 font-semibold leading-relaxed">
              {footerData.contact.address}
            </p>
          </div>
          <div className="text-left md:text-right flex flex-col justify-end">
            <p className="text-[11px] text-zinc-400 font-semibold">
              Telephone: <span className="text-zinc-700 font-bold">{footerData.contact.phone}</span>
            </p>
            <p className="text-[11px] text-zinc-400 font-semibold mt-0.5">
              Email: <span className="text-zinc-700 font-bold">{footerData.contact.email}</span>
            </p>
          </div>
        </div>

        {/* Row 4: Copyright and About Description */}
        <div className="flex flex-col md:flex-row justify-between items-center text-[11px] text-zinc-450 font-bold gap-4">
          <div className="max-w-xl text-center md:text-left font-semibold">
            {footerData.about_text}
          </div>
          <div className="text-zinc-500 whitespace-nowrap">
            {footerData.copyright}
          </div>
        </div>

      </div>
    </footer>
  );
}
