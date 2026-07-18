"use client";

import * as React from "react";
import Link from "next/link";
import { useRouter } from "next/navigation";
import { KeyRound, ArrowLeft, Send } from "lucide-react";
import { toast } from "sonner";
import { getRelativePath } from "@/lib/utils";

export default function ForgotPasswordPage() {
  const router = useRouter();

  const [email, setEmail] = React.useState("");
  const [otpCode, setOtpCode] = React.useState("");
  const [password, setPassword] = React.useState("");
  const [passwordConfirmation, setPasswordConfirmation] = React.useState("");
  
  const [step, setStep] = React.useState<"email" | "reset">("email");
  const [loading, setLoading] = React.useState(false);
  const [timer, setTimer] = React.useState(0);
  const [errorMsg, setErrorMsg] = React.useState<string | null>(null);

  const API_URL = process.env.NEXT_PUBLIC_API_URL || "http://localhost:8000";

  React.useEffect(() => {
    let interval: any;
    if (timer > 0) {
      interval = setInterval(() => {
        setTimer((prev) => prev - 1);
      }, 1000);
    }
    return () => clearInterval(interval);
  }, [timer]);

  const handleRequestOtp = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!email.trim() || !email.includes("@")) {
      toast.warning("Please enter a valid email address.");
      return;
    }
    setErrorMsg(null);
    setLoading(true);

    try {
      const response = await fetch(`${API_URL}/api/v1/auth/forgot-password`, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ email })
      });
      const resData = await response.json();
      if (!response.ok) {
        throw new Error(resData.message || "Failed to request recovery code.");
      }
      setStep("reset");
      setTimer(resData.data?.resend_delay || 60);
      toast.success("Password recovery code has been sent to your email!");
    } catch (err: any) {
      setErrorMsg(err.message || "Request failed.");
    } finally {
      setLoading(false);
    }
  };

  const handleResendOtp = async () => {
    if (timer > 0) return;
    setLoading(true);
    try {
      const response = await fetch(`${API_URL}/api/v1/auth/forgot-password`, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ email })
      });
      const resData = await response.json();
      if (!response.ok) {
        throw new Error(resData.message || "Failed to resend OTP.");
      }
      setTimer(resData.data?.resend_delay || 60);
      toast.success("OTP Code resent successfully!");
    } catch (err: any) {
      toast.error(err.message || "Resend failed.");
    } finally {
      setLoading(false);
    }
  };

  const handleResetPassword = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!otpCode) {
      setErrorMsg("Please enter the OTP verification code.");
      return;
    }
    if (password.length < 8) {
      setErrorMsg("Password must be at least 8 characters long.");
      return;
    }
    if (password !== passwordConfirmation) {
      setErrorMsg("Passwords do not match.");
      return;
    }
    setErrorMsg(null);
    setLoading(true);

    try {
      const response = await fetch(`${API_URL}/api/v1/auth/reset-password`, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          email,
          otp: otpCode,
          password,
          password_confirmation: passwordConfirmation
        })
      });
      const resData = await response.json();
      if (!response.ok) {
        throw new Error(resData.message || "Failed to reset password.");
      }
      toast.success("Password reset successful!", {
        description: "Please log in using your new password."
      });
      router.push("/login");
    } catch (err: any) {
      setErrorMsg(err.message || "Reset failed.");
    } finally {
      setLoading(false);
    }
  };

  return (
    <main className="min-h-[80vh] bg-zinc-50/50 flex items-center justify-center py-12 px-4 select-none">
      <div className="max-w-md w-full space-y-6">
        
        {/* Back Link */}
        <Link href={getRelativePath("/login")} className="inline-flex items-center gap-1.5 text-[11px] font-black uppercase tracking-wider text-zinc-450 hover:text-zinc-800 transition-colors">
          <ArrowLeft className="h-3.5 w-3.5" /> Back to Sign In
        </Link>

        {/* Form Card */}
        <div className="bg-white border border-zinc-150 rounded-2xl p-6 sm:p-8 shadow-3xs space-y-6">
          <div className="flex items-center gap-3 border-b border-zinc-100 pb-5">
            <div className="p-2.5 bg-[#f51c50]/5 rounded-xl text-[#f51c50]">
              <KeyRound className="h-5 w-5" />
            </div>
            <div>
              <span className="text-[10px] font-black uppercase tracking-widest text-[#f51c50]">Password Recovery</span>
              <h1 className="text-lg font-black text-zinc-900 uppercase tracking-wider mt-0.5">Recover Password</h1>
            </div>
          </div>

          {errorMsg && (
            <div className="bg-red-50 border border-red-200 text-red-650 text-xs p-3 rounded-xs text-center font-bold">
              {errorMsg}
            </div>
          )}

          {step === "email" ? (
            <form onSubmit={handleRequestOtp} className="space-y-4">
              <div className="flex flex-col gap-1">
                <label className="text-[10px] font-black uppercase text-zinc-450 tracking-wider">Email Address</label>
                <input 
                  type="email" 
                  value={email}
                  onChange={(e) => setEmail(e.target.value)}
                  placeholder="E.g. customer@example.com"
                  className="w-full text-xs font-semibold px-4 py-3 bg-zinc-50 border border-zinc-200 rounded-xl focus:outline-none focus:border-[#f51c50] transition-colors"
                  required
                />
              </div>

              <button
                type="submit"
                disabled={loading}
                className="w-full py-3.5 bg-[#f51c50] hover:bg-[#e01445] text-white text-[10px] font-black uppercase tracking-wider rounded-xl transition-all flex items-center justify-center gap-1.5 cursor-pointer shadow-3xs"
              >
                {loading ? "Processing..." : <>Send Reset OTP <Send className="h-3 w-3" /></>}
              </button>
            </form>
          ) : (
            <form onSubmit={handleResetPassword} className="space-y-4">
              <div className="flex flex-col gap-1">
                <label className="text-[10px] font-black uppercase text-zinc-450 tracking-wider">Verification OTP Code</label>
                <input 
                  type="text" 
                  value={otpCode}
                  onChange={(e) => setOtpCode(e.target.value)}
                  placeholder="Enter 6-digit Code"
                  className="w-full text-xs font-semibold px-4 py-3 bg-zinc-50 border border-zinc-200 rounded-xl focus:outline-none focus:border-[#f51c50] transition-colors text-center tracking-[4px]"
                  required
                />
              </div>

              <div className="flex flex-col gap-1">
                <label className="text-[10px] font-black uppercase text-zinc-450 tracking-wider">New Password</label>
                <input 
                  type="password" 
                  value={password}
                  onChange={(e) => setPassword(e.target.value)}
                  placeholder="At least 8 characters"
                  className="w-full text-xs font-semibold px-4 py-3 bg-zinc-50 border border-zinc-200 rounded-xl focus:outline-none focus:border-[#f51c50] transition-colors"
                  required
                />
              </div>

              <div className="flex flex-col gap-1">
                <label className="text-[10px] font-black uppercase text-zinc-450 tracking-wider">Confirm New Password</label>
                <input 
                  type="password" 
                  value={passwordConfirmation}
                  onChange={(e) => setPasswordConfirmation(e.target.value)}
                  placeholder="Repeat new password"
                  className="w-full text-xs font-semibold px-4 py-3 bg-zinc-50 border border-zinc-200 rounded-xl focus:outline-none focus:border-[#f51c50] transition-colors"
                  required
                />
              </div>

              <div className="flex justify-between items-center text-[11px] font-bold text-zinc-400">
                <button type="button" onClick={() => setStep("email")} className="hover:underline cursor-pointer">
                  &larr; Change Email
                </button>
                <button 
                  type="button" 
                  disabled={timer > 0 || loading} 
                  onClick={handleResendOtp}
                  className={timer > 0 ? "text-zinc-300 cursor-not-allowed" : "text-[#f51c50] hover:underline cursor-pointer"}
                >
                  {timer > 0 ? `Resend in ${timer}s` : "Resend OTP"}
                </button>
              </div>

              <button
                type="submit"
                disabled={loading}
                className="w-full py-3.5 bg-[#f51c50] hover:bg-[#e01445] text-white text-[10px] font-black uppercase tracking-wider rounded-xl transition-all flex items-center justify-center gap-1.5 cursor-pointer shadow-3xs"
              >
                {loading ? "Processing..." : "Verify & Reset Password"}
              </button>
            </form>
          )}
        </div>
      </div>
    </main>
  );
}
