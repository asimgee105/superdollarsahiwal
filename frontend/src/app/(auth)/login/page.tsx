"use client";

import * as React from "react";
import Link from "next/link";
import { useRouter } from "next/navigation";
import { useAuthStore } from "@/store/slices/authSlice";
import { authApi } from "@/features/auth/api";
import { toast } from "sonner";
import { getRelativePath } from "@/lib/utils";

export default function LoginPage() {
  const router = useRouter();
  const { setCredentials } = useAuthStore();
  
  const [loginMode, setLoginMode] = React.useState<"email" | "number">("email");
  const [authMethod, setAuthMethod] = React.useState<"password" | "otp">("password");
  const [loginInput, setLoginInput] = React.useState("");
  const [password, setPassword] = React.useState("");
  const [name, setName] = React.useState("");
  const [phone, setPhone] = React.useState("");
  const [passwordConfirmation, setPasswordConfirmation] = React.useState("");
  const [otpCode, setOtpCode] = React.useState("");
  const [step, setStep] = React.useState<"input" | "password" | "otp" | "complete-profile">("input");
  const [agree, setAgree] = React.useState(false);
  const [errorMsg, setErrorMsg] = React.useState<string | null>(null);
  const [isLoading, setIsLoading] = React.useState(false);
  const [timer, setTimer] = React.useState(0);

  const [authProviders, setAuthProviders] = React.useState({
    email_password: true,
    email_otp: true,
    mobile_password: true,
    mobile_otp: true,
    google: false,
    facebook: false,
    apple: false,
    github: false,
    microsoft: false,
    linkedin: false,
    twitter: false,
  });

  const API_URL = process.env.NEXT_PUBLIC_API_URL || "http://localhost:8000";

  React.useEffect(() => {
    fetch(`${API_URL}/api/v1/settings?nocache=1`)
      .then((res) => res.json())
      .then((data) => {
        if (data.auth_providers) {
          setAuthProviders(data.auth_providers);
        }
      })
      .catch(() => {});
  }, [API_URL]);

  React.useEffect(() => {
    let interval: any;
    if (timer > 0) {
      interval = setInterval(() => {
        setTimer((prev) => prev - 1);
      }, 1000);
    }
    return () => clearInterval(interval);
  }, [timer]);

  const isEmail = loginMode === "email";
  
  const isInputValid = isEmail 
    ? /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(loginInput) 
    : (loginInput.length >= 9 && /^\d+$/.test(loginInput));

  const handleContinue = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!isInputValid) {
      setErrorMsg(isEmail ? "Please enter a valid email address." : "Please enter a valid mobile number.");
      return;
    }
    if (!agree) {
      setErrorMsg("You must agree to the Terms of Use and Privacy Policy.");
      return;
    }
    setErrorMsg(null);
    setIsLoading(true);

    try {
      let userExists = false;
      if (isEmail) {
        const checkRes = await fetch(`${API_URL}/api/v1/auth/check-user`, {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ email: loginInput })
        });
        const checkData = await checkRes.json();
        if (checkRes.ok) {
          userExists = checkData.exists;
        }
      }

      if (!userExists && isEmail) {
        // Force OTP registration flow for new users
        setAuthMethod("otp");
        const response = await fetch(`${API_URL}/api/v1/auth/otp/send`, {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ email: loginInput })
        });
        const resData = await response.json();
        if (!response.ok) {
          throw new Error(resData.message || "Failed to send registration OTP.");
        }
        setStep("otp");
        setTimer(resData.data?.resend_delay || 60);
        toast.success("Welcome! A registration OTP has been sent to your email.");
      } else {
        if (authMethod === "otp") {
          const response = await fetch(`${API_URL}/api/v1/auth/otp/send`, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ email: loginInput })
          });
          const resData = await response.json();
          if (!response.ok) {
            throw new Error(resData.message || "Failed to send OTP.");
          }
          setStep("otp");
          setTimer(resData.data?.resend_delay || 60);
          toast.success("OTP Code has been sent to your email!");
        } else {
          setStep("password");
        }
      }
    } catch (err: any) {
      setErrorMsg(err.message || "Failed to process request. Please try again.");
    } finally {
      setIsLoading(false);
    }
  };

  const handleResendOtp = async () => {
    if (timer > 0) return;
    setIsLoading(true);
    try {
      const response = await fetch(`${API_URL}/api/v1/auth/otp/send`, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ email: loginInput })
      });
      const resData = await response.json();
      if (!response.ok) {
        throw new Error(resData.message || "Failed to resend OTP.");
      }
      setTimer(resData.data?.resend_delay || 60);
      toast.success("OTP Code resent successfully!");
    } catch (err: any) {
      toast.error(err.message || "Failed to resend OTP code.");
    } finally {
      setIsLoading(false);
    }
  };

  const handleLogin = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!password) {
      setErrorMsg("Please enter your password.");
      return;
    }
    setErrorMsg(null);
    setIsLoading(true);

    try {
      const response = await authApi.login({
        email: isEmail ? loginInput : "test@example.com",
        password: password,
      });
      setCredentials(response.data.user, response.data.token);
      router.push("/");
    } catch (err: any) {
      setErrorMsg(err.message || "Incorrect password. Please try again.");
    } finally {
      setIsLoading(false);
    }
  };

  const handleVerifyOtp = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!otpCode || otpCode.length < 4) {
      setErrorMsg("Please enter a valid OTP code.");
      return;
    }
    setErrorMsg(null);
    setIsLoading(true);

    try {
      const response = await fetch(`${API_URL}/api/v1/auth/otp/verify`, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ email: loginInput, otp: otpCode })
      });
      const resData = await response.json();
      if (!response.ok) {
        throw new Error(resData.message || "Invalid OTP code.");
      }
      
      if (resData.data.is_new_user) {
        setStep("complete-profile");
        toast.info("OTP verified! Please complete your account profile details.");
      } else {
        setCredentials(resData.data.user, resData.data.token);
        toast.success("Logged in successfully!");
        router.push("/");
      }
    } catch (err: any) {
      setErrorMsg(err.message || "Verification failed.");
    } finally {
      setIsLoading(false);
    }
  };

  const handleCompleteProfile = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!name.trim()) {
      setErrorMsg("Please enter your full name.");
      return;
    }
    if (!phone.trim()) {
      setErrorMsg("Please enter your mobile number.");
      return;
    }
    if (password.length < 8) {
      setErrorMsg("Password must be at least 8 characters.");
      return;
    }
    if (password !== passwordConfirmation) {
      setErrorMsg("Passwords do not match.");
      return;
    }
    setErrorMsg(null);
    setIsLoading(true);

    try {
      const response = await fetch(`${API_URL}/api/v1/auth/register-complete`, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          email: loginInput,
          name,
          phone,
          password,
          password_confirmation: passwordConfirmation,
        })
      });
      const resData = await response.json();
      if (!response.ok) {
        throw new Error(resData.message || "Failed to complete registration.");
      }

      setCredentials(resData.data.user, resData.data.token);
      toast.success("Registration complete! Account logged in successfully.");
      router.push("/");
    } catch (err: any) {
      setErrorMsg(err.message || "Registration completion failed.");
    } finally {
      setIsLoading(false);
    }
  };

  return (
    <div className="min-h-[85vh] w-full flex items-center justify-center bg-[#fdf2f4] py-12 px-4 select-none">
      <div className="w-full max-w-[420px] bg-white border border-zinc-150 shadow-md rounded-sm overflow-hidden flex flex-col">
        
        {/* Promotional Banner */}
        <div className="bg-[#fff3eb] p-6 flex items-center justify-between relative overflow-hidden select-none border-b border-zinc-100">
          <div className="flex flex-col gap-1.5 z-10 max-w-[65%]">
            <h3 className="text-zinc-800 font-extrabold text-xs tracking-wider uppercase">
              GET 25% OFF, UP TO $200
            </h3>
            <p className="text-[10px] text-zinc-500 font-bold uppercase tracking-wide">
              ON YOUR 1st ORDER + EXCITING OFFERS
            </p>
            <div className="mt-2 inline-block">
              <span className="border border-dashed border-[#ff3f6c] text-[#ff3f6c] bg-white text-[9px] font-black uppercase px-2.5 py-1 rounded-xs tracking-wider">
                MYNTRASAVE
              </span>
            </div>
          </div>
          
          <div className="absolute right-3 bottom-0 w-24 h-24 opacity-90">
            <svg viewBox="0 0 100 100" fill="none" className="w-full h-full">
              <circle cx="50" cy="50" r="40" fill="#ffe0cc" />
              <path d="M30 40 L60 25 L65 30 L35 45 Z" fill="#ff7f50" />
              <path d="M60 25 L75 35 L70 42 L55 32 Z" fill="#ff4500" />
              <rect x="25" y="42" width="12" height="20" rx="3" fill="#ff7f50" />
              <path d="M45 48 C45 65 60 70 65 80" stroke="#ff4500" strokeWidth="4" strokeLinecap="round" />
            </svg>
          </div>
        </div>

        {/* Form Body */}
        <div className="p-8 flex flex-col flex-grow">
          {errorMsg && (
            <div className="bg-red-50 border border-red-200 text-red-600 text-xs p-3 rounded-xs mb-5 text-center font-bold">
              {errorMsg}
            </div>
          )}

          {step === "input" && (
            <form onSubmit={handleContinue} className="flex flex-col flex-grow justify-between">
              <div>
                <h2 className="text-zinc-800 font-black text-lg tracking-wide flex items-center gap-1.5 animate-fade-in">
                  Login <span className="text-zinc-400 font-medium text-sm">or</span> Signup
                </h2>

                {/* Email / Number Switcher Buttons */}
                <div className="flex gap-2 mt-5">
                  <button
                    type="button"
                    onClick={() => {
                      setLoginMode("email");
                      setLoginInput("");
                      setErrorMsg(null);
                      setAuthMethod(authProviders.email_password ? "password" : "otp");
                    }}
                    className={`flex-grow py-2.5 text-[10px] font-black uppercase tracking-wider rounded-sm transition-all border ${
                      loginMode === "email"
                        ? "bg-[#ff3f6c] border-[#ff3f6c] text-white"
                        : "bg-white border-zinc-300 text-zinc-650 hover:bg-zinc-50"
                    } cursor-pointer`}
                  >
                    With Email
                  </button>
                  <button
                    type="button"
                    onClick={() => {
                      setLoginMode("number");
                      setLoginInput("");
                      setErrorMsg(null);
                      setAuthMethod(authProviders.mobile_password ? "password" : "otp");
                    }}
                    className={`flex-grow py-2.5 text-[10px] font-black uppercase tracking-wider rounded-sm transition-all border ${
                      loginMode === "number"
                        ? "bg-[#ff3f6c] border-[#ff3f6c] text-white"
                        : "bg-white border-zinc-300 text-zinc-650 hover:bg-zinc-50"
                    } cursor-pointer`}
                  >
                    With Number
                  </button>
                </div>

                <div className="flex items-center border border-zinc-300 rounded-sm px-3.5 py-3 focus-within:border-zinc-400 transition-colors mt-5 animate-fade-in">
                  {loginMode === "number" && (
                    <>
                      <span className="text-zinc-500 font-extrabold text-sm select-none tracking-wider">
                        +92
                      </span>
                      <span className="text-zinc-300 mx-3 select-none text-base">|</span>
                    </>
                  )}
                  <input
                    type={loginMode === "email" ? "email" : "text"}
                    placeholder={loginMode === "email" ? "Enter Email Address" : "Enter Mobile Number"}
                    value={loginInput}
                    onChange={(e) => setLoginInput(e.target.value)}
                    required
                    className="w-full text-zinc-800 font-bold outline-none text-sm placeholder-zinc-400 bg-transparent border-none p-0 focus:ring-0"
                  />
                </div>

                {/* Password / OTP Method Selection */}
                {loginMode === "email" && authProviders.email_password && authProviders.email_otp && (
                  <div className="flex gap-4 mt-5 font-bold text-[11px] text-zinc-500 select-none">
                    <label className="flex items-center gap-1.5 cursor-pointer">
                      <input 
                        type="radio" 
                        name="auth_method"
                        checked={authMethod === "password"}
                        onChange={() => setAuthMethod("password")}
                        className="text-[#ff3f6c] focus:ring-[#ff3f6c]"
                      /> Login with Password
                    </label>
                    <label className="flex items-center gap-1.5 cursor-pointer">
                      <input 
                        type="radio" 
                        name="auth_method"
                        checked={authMethod === "otp"}
                        onChange={() => setAuthMethod("otp")}
                        className="text-[#ff3f6c] focus:ring-[#ff3f6c]"
                      /> Login with OTP
                    </label>
                  </div>
                )}

                {/* Terms checkbox */}
                <div className="text-[11px] text-zinc-500 leading-relaxed font-semibold mt-7">
                  By continuing, I agree to the{" "}
                  <Link href="/terms" className="text-[#ff3f6c] font-bold hover:underline">
                    Terms of Use
                  </Link>{" "}
                  &{" "}
                  <Link href="/privacy-policy" className="text-[#ff3f6c] font-bold hover:underline">
                    Privacy Policy
                  </Link>{" "}
                  and I am above 18 years old.
                </div>

                <div className="flex items-start gap-2.5 mt-5">
                  <input
                    type="checkbox"
                    id="terms-agree"
                    checked={agree}
                    onChange={(e) => setAgree(e.target.checked)}
                    className="mt-0.5 w-4 h-4 text-[#ff3f6c] border-zinc-300 rounded-sm focus:ring-[#ff3f6c]"
                  />
                  <label htmlFor="terms-agree" className="text-[10px] text-zinc-400 font-bold select-none cursor-pointer">
                    I agree to terms and age verification limits
                  </label>
                </div>
              </div>

              <div className="mt-8">
                <button
                  type="submit"
                  disabled={!isInputValid || !agree || isLoading}
                  className={`w-full py-3.5 text-xs font-black uppercase tracking-wider rounded-sm shadow-xs transition-all duration-300 ${
                    isInputValid && agree && !isLoading
                      ? "bg-[#ff3f6c] text-white hover:bg-[#e6355f] cursor-pointer"
                      : "bg-[#8d8f9c] text-white/90 opacity-70 cursor-not-allowed"
                  }`}
                >
                  {isLoading ? "Processing..." : "Continue"}
                </button>

                {/* Dynamic Social Login Options */}
                {(authProviders.google || authProviders.facebook || authProviders.apple || authProviders.github || authProviders.microsoft || authProviders.linkedin || authProviders.twitter) && (
                  <div className="mt-6 border-t border-zinc-150 pt-4 flex flex-col gap-2.5">
                    <div className="text-center text-[10px] text-zinc-400 font-bold uppercase tracking-wider mb-1">
                      Or Sign In With
                    </div>
                    <div className="grid grid-cols-2 gap-2">
                      {authProviders.google && (
                        <button
                          type="button"
                          onClick={() => window.location.href = `${API_URL}/auth/google/redirect`}
                          className="flex items-center justify-center gap-2 py-2 px-3 border border-zinc-300 rounded-sm hover:bg-zinc-50 text-[10px] font-black uppercase tracking-wider text-zinc-700 transition-all cursor-pointer"
                        >
                          Google
                        </button>
                      )}
                      {authProviders.facebook && (
                        <button
                          type="button"
                          onClick={() => window.location.href = `${API_URL}/auth/facebook/redirect`}
                          className="flex items-center justify-center gap-2 py-2 px-3 border border-zinc-300 rounded-sm hover:bg-zinc-50 text-[10px] font-black uppercase tracking-wider text-zinc-700 transition-all cursor-pointer"
                        >
                          Facebook
                        </button>
                      )}
                      {authProviders.apple && (
                        <button
                          type="button"
                          onClick={() => window.location.href = `${API_URL}/auth/apple/redirect`}
                          className="flex items-center justify-center gap-2 py-2 px-3 border border-zinc-300 rounded-sm hover:bg-zinc-50 text-[10px] font-black uppercase tracking-wider text-zinc-700 transition-all cursor-pointer"
                        >
                          Apple
                        </button>
                      )}
                      {authProviders.github && (
                        <button
                          type="button"
                          onClick={() => window.location.href = `${API_URL}/auth/github/redirect`}
                          className="flex items-center justify-center gap-2 py-2 px-3 border border-zinc-300 rounded-sm hover:bg-zinc-50 text-[10px] font-black uppercase tracking-wider text-zinc-700 transition-all cursor-pointer"
                        >
                          GitHub
                        </button>
                      )}
                      {authProviders.microsoft && (
                        <button
                          type="button"
                          onClick={() => window.location.href = `${API_URL}/auth/microsoft/redirect`}
                          className="flex items-center justify-center gap-2 py-2 px-3 border border-zinc-300 rounded-sm hover:bg-zinc-50 text-[10px] font-black uppercase tracking-wider text-zinc-700 transition-all cursor-pointer"
                        >
                          Microsoft
                        </button>
                      )}
                      {authProviders.linkedin && (
                        <button
                          type="button"
                          onClick={() => window.location.href = `${API_URL}/auth/linkedin/redirect`}
                          className="flex items-center justify-center gap-2 py-2 px-3 border border-zinc-300 rounded-sm hover:bg-zinc-50 text-[10px] font-black uppercase tracking-wider text-zinc-700 transition-all cursor-pointer"
                        >
                          LinkedIn
                        </button>
                      )}
                      {authProviders.twitter && (
                        <button
                          type="button"
                          onClick={() => window.location.href = `${API_URL}/auth/twitter/redirect`}
                          className="flex items-center justify-center gap-2 py-2 px-3 border border-zinc-300 rounded-sm hover:bg-zinc-50 text-[10px] font-black uppercase tracking-wider text-zinc-700 transition-all cursor-pointer col-span-2"
                        >
                          X (Twitter)
                        </button>
                      )}
                    </div>
                  </div>
                )}

                <p className="text-center text-xs text-zinc-500 mt-6 font-semibold">
                  Have trouble logging in?{" "}
                  <Link href="/support" className="text-[#ff3f6c] font-bold hover:underline">
                    Get help
                  </Link>
                </p>
              </div>
            </form>
          )}

          {step === "password" && (
            <form onSubmit={handleLogin} className="flex flex-col flex-grow justify-between animate-fade-in">
              <div>
                <h2 className="text-zinc-800 font-black text-lg tracking-wide">
                  Enter Password
                </h2>
                <p className="text-xs text-zinc-500 font-bold mt-1.5 flex items-center gap-1.5">
                  Login for <span className="text-zinc-700 font-black">{loginInput}</span>
                </p>

                <div className="flex items-center border border-zinc-300 rounded-sm px-3.5 py-3 focus-within:border-zinc-400 transition-colors mt-6">
                  <input
                    type="password"
                    placeholder="Enter Password"
                    value={password}
                    onChange={(e) => setPassword(e.target.value)}
                    required
                    className="w-full text-zinc-800 font-bold outline-none text-sm placeholder-zinc-400 bg-transparent border-none p-0 focus:ring-0"
                  />
                </div>

                <div className="flex justify-between items-center mt-4">
                  <button
                    type="button"
                    onClick={() => setStep("input")}
                    className="text-[11px] text-zinc-500 font-bold hover:underline cursor-pointer"
                  >
                    &larr; Change Username
                  </button>
                  <Link href="/forgot-password" className="text-[11px] text-[#ff3f6c] font-bold hover:underline">
                    Forgot Password?
                  </Link>
                </div>
              </div>

              <div className="mt-8">
                <button
                  type="submit"
                  disabled={isLoading || !password}
                  className={`w-full py-3.5 text-xs font-black uppercase tracking-wider rounded-sm shadow-xs transition-all duration-300 ${
                    password && !isLoading
                      ? "bg-[#ff3f6c] text-white hover:bg-[#e6355f] cursor-pointer"
                      : "bg-[#8d8f9c] text-white/90 opacity-70 cursor-not-allowed"
                  }`}
                >
                  {isLoading ? "Signing In..." : "Login"}
                </button>
              </div>
            </form>
          )}

          {step === "otp" && (
            <form onSubmit={handleVerifyOtp} className="flex flex-col flex-grow justify-between animate-fade-in">
              <div>
                <h2 className="text-zinc-800 font-black text-lg tracking-wide">
                  Enter Verification Code
                </h2>
                <p className="text-xs text-zinc-500 font-bold mt-1.5 flex items-center gap-1.5">
                  OTP sent to <span className="text-zinc-700 font-black">{loginInput}</span>
                </p>

                <div className="flex items-center border border-zinc-300 rounded-sm px-3.5 py-3 focus-within:border-zinc-400 transition-colors mt-6">
                  <input
                    type="text"
                    placeholder="Enter Code"
                    value={otpCode}
                    onChange={(e) => setOtpCode(e.target.value)}
                    required
                    className="w-full text-zinc-800 font-bold outline-none text-sm placeholder-zinc-400 bg-transparent border-none p-0 focus:ring-0 tracking-[4px] text-center"
                  />
                </div>

                <div className="flex justify-between items-center mt-4">
                  <button
                    type="button"
                    onClick={() => {
                      setStep("input");
                      setOtpCode("");
                    }}
                    className="text-[11px] text-zinc-500 font-bold hover:underline cursor-pointer"
                  >
                    &larr; Change Email
                  </button>

                  <button
                    type="button"
                    disabled={timer > 0 || isLoading}
                    onClick={handleResendOtp}
                    className={`text-[11px] font-bold ${
                      timer > 0 ? "text-zinc-400 cursor-not-allowed" : "text-[#ff3f6c] hover:underline cursor-pointer"
                    }`}
                  >
                    {timer > 0 ? `Resend OTP in ${timer}s` : "Resend OTP"}
                  </button>
                </div>
              </div>

              <div className="mt-8">
                <button
                  type="submit"
                  disabled={isLoading || !otpCode}
                  className={`w-full py-3.5 text-xs font-black uppercase tracking-wider rounded-sm shadow-xs transition-all duration-300 ${
                    otpCode && !isLoading
                      ? "bg-[#ff3f6c] text-white hover:bg-[#e6355f] cursor-pointer"
                      : "bg-[#8d8f9c] text-white/90 opacity-70 cursor-not-allowed"
                  }`}
                >
                  {isLoading ? "Verifying..." : "Verify & Login"}
                </button>
              </div>
            </form>
          )}

          {step === "complete-profile" && (
            <form onSubmit={handleCompleteProfile} className="flex flex-col flex-grow justify-between animate-fade-in">
              <div>
                <h2 className="text-zinc-800 font-black text-lg tracking-wide">
                  Complete Your Profile
                </h2>
                <p className="text-xs text-zinc-500 font-bold mt-1.5 flex items-center gap-1.5">
                  Final details for <span className="text-zinc-700 font-black">{loginInput}</span>
                </p>

                {/* Name Input */}
                <div className="flex items-center border border-zinc-300 rounded-sm px-3.5 py-3 focus-within:border-zinc-400 transition-colors mt-6">
                  <input
                    type="text"
                    placeholder="Enter Full Name"
                    value={name}
                    onChange={(e) => setName(e.target.value)}
                    required
                    className="w-full text-zinc-800 font-bold outline-none text-sm placeholder-zinc-400 bg-transparent border-none p-0 focus:ring-0"
                  />
                </div>

                {/* Mobile Phone Input */}
                <div className="flex items-center border border-zinc-300 rounded-sm px-3.5 py-3 focus-within:border-zinc-400 transition-colors mt-4">
                  <input
                    type="text"
                    placeholder="Enter Mobile Number"
                    value={phone}
                    onChange={(e) => setPhone(e.target.value)}
                    required
                    className="w-full text-zinc-800 font-bold outline-none text-sm placeholder-zinc-400 bg-transparent border-none p-0 focus:ring-0"
                  />
                </div>

                {/* Password Input */}
                <div className="flex items-center border border-zinc-300 rounded-sm px-3.5 py-3 focus-within:border-zinc-400 transition-colors mt-4">
                  <input
                    type="password"
                    placeholder="Create Password (min 8 chars)"
                    value={password}
                    onChange={(e) => setPassword(e.target.value)}
                    required
                    className="w-full text-zinc-800 font-bold outline-none text-sm placeholder-zinc-400 bg-transparent border-none p-0 focus:ring-0"
                  />
                </div>

                {/* Confirm Password Input */}
                <div className="flex items-center border border-zinc-300 rounded-sm px-3.5 py-3 focus-within:border-zinc-400 transition-colors mt-4">
                  <input
                    type="password"
                    placeholder="Confirm Password"
                    value={passwordConfirmation}
                    onChange={(e) => setPasswordConfirmation(e.target.value)}
                    required
                    className="w-full text-zinc-800 font-bold outline-none text-sm placeholder-zinc-400 bg-transparent border-none p-0 focus:ring-0"
                  />
                </div>
              </div>

              <div className="mt-8">
                <button
                  type="submit"
                  disabled={isLoading || !name || !phone || !password || !passwordConfirmation}
                  className={`w-full py-3.5 text-xs font-black uppercase tracking-wider rounded-sm shadow-xs transition-all duration-300 ${
                    name && phone && password && passwordConfirmation && !isLoading
                      ? "bg-[#ff3f6c] text-white hover:bg-[#e6355f] cursor-pointer"
                      : "bg-[#8d8f9c] text-white/90 opacity-70 cursor-not-allowed"
                  }`}
                >
                  {isLoading ? "Saving..." : "Save & Complete"}
                </button>
              </div>
            </form>
          )}
        </div>

      </div>
    </div>
  );
}
