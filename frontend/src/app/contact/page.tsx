"use client";

import * as React from "react";
import Link from "next/link";
import { Mail, Phone, MapPin, ArrowLeft, Send } from "lucide-react";
import { toast } from "sonner";
import { getRelativePath } from "@/lib/utils";

export default function ContactPage() {
  const [name, setName] = React.useState("");
  const [email, setEmail] = React.useState("");
  const [message, setMessage] = React.useState("");
  const [submitting, setSubmitting] = React.useState(false);

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    if (!name.trim() || !email.trim() || !message.trim()) {
      toast.warning("Please fill out all fields.");
      return;
    }
    setSubmitting(true);
    setTimeout(() => {
      toast.success("Feedback submitted successfully!", {
        description: "Our customer success support team will contact you shortly."
      });
      setName("");
      setEmail("");
      setMessage("");
      setSubmitting(false);
    }, 1200);
  };

  return (
    <main className="min-h-screen bg-zinc-50/50 py-12 px-4 sm:px-6 lg:px-8 font-sans select-none">
      <div className="max-w-4xl mx-auto space-y-6">
        
        {/* Back Link */}
        <Link href={getRelativePath("/")} className="inline-flex items-center gap-1.5 text-[11px] font-black uppercase tracking-wider text-zinc-450 hover:text-zinc-800 transition-colors">
          <ArrowLeft className="h-3.5 w-3.5" /> Back to Storefront
        </Link>

        <div className="grid grid-cols-1 md:grid-cols-5 gap-6">
          
          {/* Left Column: Coordinates */}
          <div className="bg-white border border-zinc-150 rounded-2xl p-6 sm:p-8 shadow-3xs space-y-6 md:col-span-2">
            <div>
              <span className="text-[10px] font-black uppercase tracking-widest text-[#f51c50]">Support Desk</span>
              <h1 className="text-lg font-black text-zinc-900 uppercase tracking-wider mt-0.5">Contact Coordinates</h1>
            </div>

            <div className="space-y-4 pt-4">
              <div className="flex items-start gap-3.5">
                <div className="p-2 bg-zinc-100 rounded-lg text-zinc-650">
                  <Phone className="h-4 w-4" />
                </div>
                <div className="flex flex-col">
                  <span className="text-[10px] font-black uppercase text-zinc-400">Phone Support</span>
                  <span className="text-xs font-black text-zinc-850">0300-1234567</span>
                </div>
              </div>

              <div className="flex items-start gap-3.5">
                <div className="p-2 bg-zinc-100 rounded-lg text-zinc-650">
                  <Mail className="h-4 w-4" />
                </div>
                <div className="flex flex-col">
                  <span className="text-[10px] font-black uppercase text-zinc-400">Email Address</span>
                  <span className="text-xs font-black text-zinc-850">support@aura.com</span>
                </div>
              </div>

              <div className="flex items-start gap-3.5">
                <div className="p-2 bg-zinc-100 rounded-lg text-zinc-650">
                  <MapPin className="h-4 w-4" />
                </div>
                <div className="flex flex-col">
                  <span className="text-[10px] font-black uppercase text-zinc-400">Registered Office</span>
                  <span className="text-xs font-black text-zinc-850">Gulberg, Lahore, Pakistan</span>
                </div>
              </div>
            </div>
          </div>

          {/* Right Column: Contact Form */}
          <div className="bg-white border border-zinc-150 rounded-2xl p-6 sm:p-8 shadow-3xs space-y-6 md:col-span-3">
            <div>
              <span className="text-[10px] font-black uppercase tracking-widest text-[#f51c50]">Feedback</span>
              <h1 className="text-lg font-black text-zinc-900 uppercase tracking-wider mt-0.5">Send a Message</h1>
            </div>

            <form onSubmit={handleSubmit} className="space-y-4">
              <div className="flex flex-col gap-1">
                <label className="text-[10px] font-black uppercase text-zinc-450 tracking-wider">Your Name</label>
                <input 
                  type="text" 
                  value={name}
                  onChange={(e) => setName(e.target.value)}
                  placeholder="E.g. Ali Khan"
                  className="w-full text-xs font-semibold px-4 py-3 bg-zinc-50 border border-zinc-200 rounded-xl focus:outline-none focus:border-[#f51c50] transition-colors"
                />
              </div>

              <div className="flex flex-col gap-1">
                <label className="text-[10px] font-black uppercase text-zinc-450 tracking-wider">Email Address</label>
                <input 
                  type="email" 
                  value={email}
                  onChange={(e) => setEmail(e.target.value)}
                  placeholder="E.g. ali@example.com"
                  className="w-full text-xs font-semibold px-4 py-3 bg-zinc-50 border border-zinc-200 rounded-xl focus:outline-none focus:border-[#f51c50] transition-colors"
                />
              </div>

              <div className="flex flex-col gap-1">
                <label className="text-[10px] font-black uppercase text-zinc-450 tracking-wider">Message Description</label>
                <textarea 
                  value={message}
                  onChange={(e) => setMessage(e.target.value)}
                  placeholder="Type your message details here..."
                  rows={4}
                  className="w-full text-xs font-semibold px-4 py-3 bg-zinc-50 border border-zinc-200 rounded-xl focus:outline-none focus:border-[#f51c50] transition-colors"
                />
              </div>

              <button
                type="submit"
                disabled={submitting}
                className="w-full py-3.5 bg-[#f51c50] hover:bg-[#e01445] text-white text-[10px] font-black uppercase tracking-wider rounded-xl transition-all flex items-center justify-center gap-1.5 cursor-pointer shadow-3xs"
              >
                {submitting ? "Sending..." : <>Send Message <Send className="h-3 w-3" /></>}
              </button>
            </form>
          </div>

        </div>
      </div>
    </main>
  );
}
