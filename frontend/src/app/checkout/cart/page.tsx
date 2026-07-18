"use client";

import * as React from "react";
import Link from "next/link";
import Image from "next/image";
import { getRelativePath } from "@/lib/utils";
import { useCartStore } from "@/store/useCartStore";
import { 
  Trash2, 
  Minus, 
  Plus, 
  Tag, 
  ShieldCheck, 
  ShoppingBag, 
  ArrowRight, 
  ArrowLeft,
  CheckCircle,
  Truck,
  Copy,
  Check
} from "lucide-react";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { toast } from "sonner";
import { useRouter } from "next/navigation";

export default function CartPage() {
  const cart = useCartStore((state) => state.cart);
  const removeFromCart = useCartStore((state) => state.removeFromCart);
  const clearCart = useCartStore((state) => state.clearCart);
  const router = useRouter();

  const [mounted, setMounted] = React.useState(false);
  const [step, setStep] = React.useState<"cart" | "checkout" | "success">("cart");
  const [trackingNumber, setTrackingNumber] = React.useState("");
  const [copied, setCopied] = React.useState(false);

  // Form States
  const [fullName, setFullName] = React.useState("");
  const [email, setEmail] = React.useState("");
  const [phone, setPhone] = React.useState("");
  const [address, setAddress] = React.useState("");
  const [city, setCity] = React.useState("");
  const [stateName, setStateName] = React.useState("");
  const [pincode, setPincode] = React.useState("");
  const [paymentMethod, setPaymentMethod] = React.useState<"cod" | "card" | "googlepay" | "paypal" | "applepay">("cod");
  const [gateways, setGateways] = React.useState({
    cod: true,
    stripe: true,
    googlepay: true,
    paypal: true,
    applepay: true,
  });

  // Coupon States
  const [couponCode, setCouponCode] = React.useState("");
  const [discountPercent, setDiscountPercent] = React.useState(0);
  const [appliedCoupon, setAppliedCoupon] = React.useState<string | null>(null);

  React.useEffect(() => {
    setMounted(true);
  }, []);

  const API_URL = process.env.NEXT_PUBLIC_API_URL || "http://localhost:8000";

  React.useEffect(() => {
    fetch(`${API_URL}/api/v1/settings?nocache=1`)
      .then((res) => res.json())
      .then((data) => {
        if (data.payment_gateways) {
          setGateways(data.payment_gateways);
          if (!data.payment_gateways.cod && data.payment_gateways.stripe) {
            setPaymentMethod("card");
          } else if (data.payment_gateways.cod) {
            setPaymentMethod("cod");
          } else if (data.payment_gateways.googlepay) {
            setPaymentMethod("googlepay");
          } else if (data.payment_gateways.paypal) {
            setPaymentMethod("paypal");
          } else if (data.payment_gateways.applepay) {
            setPaymentMethod("applepay");
          }
        }
      })
      .catch(() => {});
  }, [API_URL]);

  const updateQuantity = (itemId: string, currentQty: number, delta: number) => {
    const newQty = currentQty + delta;
    if (newQty <= 0) {
      removeFromCart(itemId);
      toast.info("Item removed from bag");
      return;
    }
    useCartStore.setState((state) => ({
      cart: state.cart.map((item) => 
        item.id === itemId ? { ...item, quantity: newQty } : item
      ),
    }));
  };

  const applyCoupon = (e: React.FormEvent) => {
    e.preventDefault();
    const normalized = couponCode.trim().toUpperCase();
    const match = normalized.match(/^PROMO(\d+)$/);
    if (match) {
      const num = parseInt(match[1], 10);
      if (num >= 1 && num <= 100) {
        setDiscountPercent(10);
        setAppliedCoupon(normalized);
        setCouponCode("");
        toast.success(`Coupon "${normalized}" applied!`, {
          description: "10% discount has been applied to your total.",
        });
        return;
      }
    }
    toast.error("Invalid Coupon Code");
  };

  const removeCoupon = () => {
    setDiscountPercent(0);
    setAppliedCoupon(null);
    toast.info("Coupon removed");
  };

  const handleProceedToCheckout = () => {
    if (cart.length === 0) {
      toast.warning("Your bag is empty!");
      return;
    }
    setStep("checkout");
  };

  const handlePlaceOrder = (e: React.FormEvent) => {
    e.preventDefault();
    
    // Field validations
    if (!fullName.trim() || !email.trim() || !phone.trim() || !address.trim() || !city.trim() || !stateName.trim() || !pincode.trim()) {
      toast.error("Missing Details", {
        description: "Please fill out all personal and shipping address fields.",
      });
      return;
    }

    if (phone.length < 10) {
      toast.error("Invalid Phone Number", {
        description: "Please enter a valid phone number.",
      });
      return;
    }

    if (pincode.length < 5) {
      toast.error("Invalid Pincode", {
        description: "Please enter a valid postal code.",
      });
      return;
    }

    // Generate random tracking number
    const trk = `AURA-TRK-${Math.floor(100000000 + Math.random() * 900000000)}`;
    setTrackingNumber(trk);

    // Save details to backend (simulated)
    toast.success("Order Placed Successfully!");
    
    // Save generated tracking code to session storage so /track page can access it locally if needed
    if (typeof window !== "undefined") {
      sessionStorage.setItem(trk, JSON.stringify({
        fullName,
        address: `${address}, ${city}, ${stateName} - ${pincode}`,
        items: cart.map(i => ({ title: i.title, quantity: i.quantity, price: i.price, image: i.image, brand: i.brand })),
        total: totalMRP - couponDiscount + shippingFee,
        status: "Order Placed",
        date: new Date().toLocaleDateString()
      }));
    }

    clearCart();
    setStep("success");
  };

  const copyTrackingNumber = () => {
    navigator.clipboard.writeText(trackingNumber);
    setCopied(true);
    toast.success("Tracking number copied!");
    setTimeout(() => setCopied(false), 2000);
  };

  if (!mounted) {
    return (
      <div className="mx-auto max-w-7xl px-4 py-20 text-center text-zinc-400 font-bold uppercase tracking-widest text-xs animate-pulse">
        Loading Bag...
      </div>
    );
  }

  // Price Calculations
  const totalMRP = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
  const couponDiscount = Math.round(totalMRP * (discountPercent / 100));
  const shippingFee = totalMRP > 1500 ? 0 : 150;
  const grandTotal = totalMRP - couponDiscount + shippingFee;

  return (
    <div className="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8 bg-[#fafafa] min-h-screen text-zinc-800 text-left">
      
      {/* Checkout Steps Header */}
      {step !== "success" && (
        <div className="flex items-center justify-center gap-4 sm:gap-8 pb-10 select-none text-[10px] font-black uppercase tracking-widest text-zinc-400">
          <span className={`pb-2 border-b-2 ${step === "cart" ? "text-[#f51c50] border-[#f51c50]" : "text-zinc-650 border-zinc-200"}`}>
            1. Bag
          </span>
          <ArrowRight className="h-4.5 w-4.5 text-zinc-300" />
          <span className={`pb-2 border-b-2 ${step === "checkout" ? "text-[#f51c50] border-[#f51c50]" : "text-zinc-400 border-transparent"}`}>
            2. Shipping & Payment
          </span>
          <ArrowRight className="h-4.5 w-4.5 text-zinc-300" />
          <span className="pb-2 border-b-2 border-transparent">
            3. Success
          </span>
        </div>
      )}

      {step === "cart" && (
        <>
          <div className="flex items-center gap-3 mb-8">
            <ShoppingBag className="h-6 w-6 text-[#f51c50]" />
            <h1 className="text-2xl font-black uppercase tracking-widest text-zinc-950">
              Shopping Bag ({cart.reduce((sum, i) => sum + i.quantity, 0)})
            </h1>
          </div>

          {cart.length === 0 ? (
            <div className="flex flex-col items-center justify-center py-20 bg-white border border-zinc-150 rounded-2xl p-8 max-w-lg mx-auto shadow-xs select-none">
              <div className="h-16 w-16 bg-[#f51c50]/5 rounded-full flex items-center justify-center mb-6">
                <ShoppingBag className="h-8 w-8 text-[#f51c50]" />
              </div>
              <h2 className="text-lg font-black uppercase tracking-wider text-zinc-900">Your Bag is Empty</h2>
              <p className="text-xs text-zinc-450 font-semibold text-center mt-2 max-w-xs leading-relaxed">
                You haven&apos;t added any items to your shopping bag yet. Explore our latest designer suits.
              </p>
              <Link href={getRelativePath("/catalog/")} className="mt-8 w-full">
                <Button className="w-full bg-[#f51c50] hover:bg-[#f51c50]/90 text-white font-black text-xs uppercase tracking-widest py-6 rounded-xl shadow-xs gap-2">
                  Explore Catalog <ArrowRight className="h-4 w-4" />
                </Button>
              </Link>
            </div>
          ) : (
            <div className="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
              {/* Left Column: Items */}
              <div className="lg:col-span-8 flex flex-col gap-4">
                {cart.map((item) => (
                  <div key={item.id} className="bg-white border border-zinc-150 rounded-2xl p-4 sm:p-5 flex gap-4 relative shadow-2xs hover:border-zinc-200 transition-all">
                    <div className="relative w-24 aspect-[3/4] rounded-xl overflow-hidden bg-zinc-50 flex-none border border-zinc-100">
                      <Image src={item.image} alt={item.title} fill sizes="96px" className="object-cover" />
                    </div>
                    <div className="flex flex-col justify-between flex-grow text-left">
                      <div>
                        <div className="flex justify-between items-start gap-4">
                          <span className="text-[10px] font-black uppercase tracking-widest text-[#f51c50]">{item.brand}</span>
                          <button onClick={() => removeFromCart(item.id)} className="text-zinc-400 hover:text-[#f51c50] transition-colors p-1">
                            <Trash2 className="h-4.5 w-4.5" />
                          </button>
                        </div>
                        <h3 className="text-xs sm:text-sm font-bold text-zinc-900 mt-1 max-w-md">{item.title}</h3>
                        <div className="flex flex-wrap gap-2 mt-2 select-none">
                          {item.size && <span className="text-[9.5px] font-black uppercase tracking-wider bg-zinc-50 border border-zinc-100 px-2 py-0.5 rounded text-zinc-500">Size: {item.size}</span>}
                          {item.color && <span className="text-[9.5px] font-black uppercase tracking-wider bg-zinc-50 border border-zinc-100 px-2 py-0.5 rounded text-zinc-500">Color: {item.color}</span>}
                        </div>
                      </div>
                      <div className="flex justify-between items-center mt-4">
                        <div className="flex items-center border border-zinc-200 rounded-lg p-1 bg-zinc-50/50 select-none">
                          <button onClick={() => updateQuantity(item.id, item.quantity, -1)} className="p-1 hover:bg-white rounded text-zinc-500 hover:text-black transition-colors">
                            <Minus className="h-3 w-3" />
                          </button>
                          <span className="w-8 text-center text-xs font-black text-zinc-900">{item.quantity}</span>
                          <button onClick={() => updateQuantity(item.id, item.quantity, 1)} className="p-1 hover:bg-white rounded text-zinc-500 hover:text-black transition-colors">
                            <Plus className="h-3 w-3" />
                          </button>
                        </div>
                        <span className="text-sm font-black text-zinc-950">Rs. {item.price * item.quantity}</span>
                      </div>
                    </div>
                  </div>
                ))}
              </div>

              {/* Right Column: Checkout Summary */}
              <div className="lg:col-span-4 flex flex-col gap-6">
                <div className="bg-white border border-zinc-150 rounded-2xl p-5 shadow-2xs">
                  <h3 className="text-[10px] font-black uppercase tracking-widest text-zinc-450 mb-3.5 flex items-center gap-1.5"><Tag className="h-4 w-4" /> Apply Promo Code</h3>
                  {appliedCoupon ? (
                    <div className="flex items-center justify-between bg-emerald-50 border border-emerald-100 rounded-xl p-3 select-none">
                      <div className="flex items-center gap-2">
                        <Tag className="h-4 w-4 text-emerald-600 fill-emerald-600/10" />
                        <span className="text-xs font-black text-emerald-800 tracking-wider">{appliedCoupon} Applied</span>
                      </div>
                      <button onClick={removeCoupon} className="text-xs font-bold text-zinc-400 hover:text-[#f51c50] transition-colors">Remove</button>
                    </div>
                  ) : (
                    <form onSubmit={applyCoupon} className="flex gap-2">
                      <Input type="text" placeholder="Enter code (e.g. PROMO1)" value={couponCode} onChange={(e) => setCouponCode(e.target.value)} className="text-xs rounded-xl border-zinc-200 focus-visible:ring-[#f51c50] py-5 uppercase placeholder:normal-case shadow-3xs" />
                      <Button type="submit" variant="ghost" className="text-[#f51c50] font-black text-xs uppercase hover:bg-[#f51c50]/5 px-4 tracking-wider">Apply</Button>
                    </form>
                  )}
                </div>

                <div className="bg-white border border-zinc-150 rounded-2xl p-5 shadow-2xs">
                  <h3 className="text-[10px] font-black uppercase tracking-widest text-zinc-450 mb-5 select-none">Price Details ({cart.reduce((sum, i) => sum + i.quantity, 0)} Items)</h3>
                  <div className="space-y-4 text-xs font-bold text-zinc-650 select-none">
                    <div className="flex justify-between"><span>Total MRP</span><span className="text-zinc-900 font-extrabold">Rs. {totalMRP}</span></div>
                    {couponDiscount > 0 && <div className="flex justify-between text-emerald-600"><span>Coupon Discount</span><span className="font-extrabold">- Rs. {couponDiscount}</span></div>}
                    <div className="flex justify-between">
                      <span>Shipping Fee</span>
                      {shippingFee === 0 ? <span className="text-emerald-600 font-extrabold uppercase">Free</span> : <span className="text-zinc-900 font-extrabold">Rs. 150</span>}
                    </div>
                    <hr className="border-zinc-100 my-4" />
                    <div className="flex justify-between text-sm text-zinc-950 font-black pt-1"><span>Total Amount</span><span className="text-base">Rs. {grandTotal}</span></div>
                  </div>
                  <Button onClick={handleProceedToCheckout} className="w-full bg-[#f51c50] hover:bg-[#f51c50]/95 text-white font-black text-xs uppercase tracking-widest py-6.5 rounded-xl shadow-md gap-2 mt-6 transition-all hover:scale-101">
                    Proceed to Shipping <ArrowRight className="h-4 w-4" />
                  </Button>
                  <div className="flex items-center justify-center gap-1.5 mt-4 text-[9.5px] text-zinc-400 font-bold select-none"><ShieldCheck className="h-4 w-4 text-emerald-600" /> Secure Payments Powered by Stripe</div>
                </div>
              </div>
            </div>
          )}
        </>
      )}

      {step === "checkout" && (
        <div className="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
          
          {/* Left Column: Checkout Details Form */}
          <form onSubmit={handlePlaceOrder} className="lg:col-span-8 space-y-6">
            
            {/* Back Button */}
            <button 
              type="button" 
              onClick={() => setStep("cart")}
              className="flex items-center gap-2 text-xs font-black uppercase text-zinc-400 hover:text-zinc-800 transition-colors select-none mb-2"
            >
              <ArrowLeft className="h-4 w-4" /> Back to Bag
            </button>

            {/* Personal Details Card */}
            <div className="bg-white border border-zinc-150 rounded-2xl p-5 sm:p-6 shadow-2xs space-y-4">
              <h2 className="text-sm font-black uppercase tracking-widest text-zinc-900 border-b border-zinc-100 pb-3 mb-2">
                1. Personal Details
              </h2>
              
              <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div className="space-y-1.5">
                  <label className="text-[10px] font-black uppercase tracking-wider text-zinc-450 block">Full Name</label>
                  <Input 
                    type="text" 
                    placeholder="E.g. Ali Ahmed" 
                    value={fullName}
                    onChange={(e) => setFullName(e.target.value)}
                    className="text-xs rounded-xl py-5 border-zinc-200 focus-visible:ring-[#f51c50] shadow-3xs"
                    required
                  />
                </div>
                <div className="space-y-1.5">
                  <label className="text-[10px] font-black uppercase tracking-wider text-zinc-450 block">Phone Number</label>
                  <Input 
                    type="tel" 
                    placeholder="E.g. 03001234567" 
                    value={phone}
                    onChange={(e) => setPhone(e.target.value.replace(/\D/g, ""))}
                    className="text-xs rounded-xl py-5 border-zinc-200 focus-visible:ring-[#f51c50] shadow-3xs"
                    required
                  />
                </div>
              </div>
              <div className="space-y-1.5">
                <label className="text-[10px] font-black uppercase tracking-wider text-zinc-450 block">Email Address</label>
                <Input 
                  type="email" 
                  placeholder="E.g. ali@example.com" 
                  value={email}
                  onChange={(e) => setEmail(e.target.value)}
                  className="text-xs rounded-xl py-5 border-zinc-200 focus-visible:ring-[#f51c50] shadow-3xs"
                  required
                />
              </div>
            </div>

            {/* Shipping Address Card */}
            <div className="bg-white border border-zinc-150 rounded-2xl p-5 sm:p-6 shadow-2xs space-y-4">
              <h2 className="text-sm font-black uppercase tracking-widest text-zinc-900 border-b border-zinc-100 pb-3 mb-2">
                2. Shipping Address
              </h2>
              
              <div className="space-y-1.5">
                <label className="text-[10px] font-black uppercase tracking-wider text-zinc-450 block">Street Address</label>
                <Input 
                  type="text" 
                  placeholder="E.g. House 44-A, Street 3, Sector G-10" 
                  value={address}
                  onChange={(e) => setAddress(e.target.value)}
                  className="text-xs rounded-xl py-5 border-zinc-200 focus-visible:ring-[#f51c50] shadow-3xs"
                  required
                />
              </div>

              <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div className="space-y-1.5">
                  <label className="text-[10px] font-black uppercase tracking-wider text-zinc-450 block">City</label>
                  <Input 
                    type="text" 
                    placeholder="E.g. Lahore" 
                    value={city}
                    onChange={(e) => setCity(e.target.value)}
                    className="text-xs rounded-xl py-5 border-zinc-200 focus-visible:ring-[#f51c50] shadow-3xs"
                    required
                  />
                </div>
                <div className="space-y-1.5">
                  <label className="text-[10px] font-black uppercase tracking-wider text-zinc-450 block">State / Province</label>
                  <Input 
                    type="text" 
                    placeholder="E.g. Punjab" 
                    value={stateName}
                    onChange={(e) => setStateName(e.target.value)}
                    className="text-xs rounded-xl py-5 border-zinc-200 focus-visible:ring-[#f51c50] shadow-3xs"
                    required
                  />
                </div>
                <div className="space-y-1.5">
                  <label className="text-[10px] font-black uppercase tracking-wider text-zinc-450 block">Pincode / Zip</label>
                  <Input 
                    type="text" 
                    placeholder="E.g. 54000" 
                    value={pincode}
                    onChange={(e) => setPincode(e.target.value.replace(/\D/g, ""))}
                    className="text-xs rounded-xl py-5 border-zinc-200 focus-visible:ring-[#f51c50] shadow-3xs"
                    required
                  />
                </div>
              </div>
            </div>

            {/* Payment Method Card */}
            <div className="bg-white border border-zinc-150 rounded-2xl p-5 sm:p-6 shadow-2xs space-y-4">
              <h2 className="text-sm font-black uppercase tracking-widest text-zinc-900 border-b border-zinc-100 pb-3 mb-2">
                3. Payment Method
              </h2>
              
              <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                {gateways.cod && (
                  <button
                    type="button"
                    onClick={() => setPaymentMethod("cod")}
                    className={`p-4 border rounded-xl flex flex-col text-left transition-all ${
                      paymentMethod === "cod"
                        ? "border-[#f51c50] bg-[#f51c50]/5"
                        : "border-zinc-250 hover:border-zinc-400"
                    }`}
                  >
                    <span className="text-xs font-black uppercase tracking-wide">Cash on Delivery (COD)</span>
                    <span className="text-[10px] text-zinc-450 font-bold mt-1">Pay with cash upon delivery.</span>
                  </button>
                )}
                {gateways.stripe && (
                  <button
                    type="button"
                    onClick={() => setPaymentMethod("card")}
                    className={`p-4 border rounded-xl flex flex-col text-left transition-all ${
                      paymentMethod === "card"
                        ? "border-[#f51c50] bg-[#f51c50]/5"
                        : "border-zinc-250 hover:border-zinc-400"
                    }`}
                  >
                    <span className="text-xs font-black uppercase tracking-wide">Online Card Payment</span>
                    <span className="text-[10px] text-zinc-450 font-bold mt-1">Secure payment using Stripe / Card.</span>
                  </button>
                )}
                {gateways.googlepay && (
                  <button
                    type="button"
                    onClick={() => setPaymentMethod("googlepay")}
                    className={`p-4 border rounded-xl flex flex-col text-left transition-all ${
                      paymentMethod === "googlepay"
                        ? "border-[#f51c50] bg-[#f51c50]/5"
                        : "border-zinc-250 hover:border-zinc-400"
                    }`}
                  >
                    <span className="text-xs font-black uppercase tracking-wide">Pay with Google</span>
                    <span className="text-[10px] text-zinc-450 font-bold mt-1">Quick checkout with Google Pay account.</span>
                  </button>
                )}
                {gateways.paypal && (
                  <button
                    type="button"
                    onClick={() => setPaymentMethod("paypal")}
                    className={`p-4 border rounded-xl flex flex-col text-left transition-all ${
                      paymentMethod === "paypal"
                        ? "border-[#f51c50] bg-[#f51c50]/5"
                        : "border-zinc-250 hover:border-zinc-400"
                    }`}
                  >
                    <span className="text-xs font-black uppercase tracking-wide">PayPal Express</span>
                    <span className="text-[10px] text-zinc-450 font-bold mt-1">Pay safely using your PayPal wallet.</span>
                  </button>
                )}
                {gateways.applepay && (
                  <button
                    type="button"
                    onClick={() => setPaymentMethod("applepay")}
                    className={`p-4 border rounded-xl flex flex-col text-left transition-all ${
                      paymentMethod === "applepay"
                        ? "border-[#f51c50] bg-[#f51c50]/5"
                        : "border-zinc-250 hover:border-zinc-400"
                    }`}
                  >
                    <span className="text-xs font-black uppercase tracking-wide">Apple Pay</span>
                    <span className="text-[10px] text-zinc-450 font-bold mt-1">Fast, secure checkout using Apple wallet.</span>
                  </button>
                )}
              </div>
            </div>

          </form>

          {/* Right Column: Sticky Summary */}
          <div className="lg:col-span-4 flex flex-col gap-6">
            <div className="bg-white border border-zinc-150 rounded-2xl p-5 shadow-2xs">
              <h3 className="text-[10px] font-black uppercase tracking-widest text-zinc-400 mb-5 select-none">
                Price Details
              </h3>
              <div className="space-y-4 text-xs font-bold text-zinc-650 select-none">
                <div className="flex justify-between"><span>Total MRP</span><span className="text-zinc-900 font-extrabold">Rs. {totalMRP}</span></div>
                {couponDiscount > 0 && <div className="flex justify-between text-emerald-600"><span>Coupon Discount</span><span className="font-extrabold">- Rs. {couponDiscount}</span></div>}
                <div className="flex justify-between">
                  <span>Shipping Fee</span>
                  {shippingFee === 0 ? <span className="text-emerald-600 font-extrabold uppercase">Free</span> : <span className="text-zinc-900 font-extrabold">Rs. 150</span>}
                </div>
                <hr className="border-zinc-100 my-4" />
                <div className="flex justify-between text-sm text-zinc-950 font-black pt-1"><span>Total Amount</span><span className="text-base">Rs. {grandTotal}</span></div>
              </div>
              
              <Button 
                onClick={handlePlaceOrder}
                className="w-full bg-[#f51c50] hover:bg-[#f51c50]/95 text-white font-black text-xs uppercase tracking-widest py-6.5 rounded-xl shadow-md gap-2 mt-6 transition-all hover:scale-101"
              >
                Confirm & Place Order <Check className="h-4 w-4" />
              </Button>
            </div>
          </div>

        </div>
      )}

      {step === "success" && (
        <div className="max-w-2xl mx-auto bg-white border border-zinc-150 rounded-2xl p-8 shadow-xs text-center select-none">
          <div className="h-16 w-16 bg-emerald-500/10 rounded-full flex items-center justify-center mx-auto mb-6">
            <CheckCircle className="h-8 w-8 text-emerald-600" />
          </div>
          
          <h1 className="text-xl sm:text-2xl font-black uppercase tracking-widest text-zinc-900">
            Order Placed Successfully!
          </h1>
          <p className="text-xs text-zinc-450 font-bold mt-2 leading-relaxed max-w-md mx-auto">
            Thank you for shopping with AURA. Your package details have been saved and dispatched to shipping hubs.
          </p>

          {/* Tracking Details card */}
          <div className="mt-8 p-5 bg-zinc-50 border border-zinc-150 rounded-2xl max-w-lg mx-auto space-y-3.5 text-left">
            <div className="flex justify-between items-center">
              <span className="text-[10px] font-black uppercase text-zinc-450 tracking-wider">Tracking Number</span>
              <div className="flex items-center gap-1.5">
                <span className="text-xs font-black text-zinc-900 select-all">{trackingNumber}</span>
                <button 
                  onClick={copyTrackingNumber}
                  className="p-1 hover:bg-zinc-200 rounded text-zinc-500 transition-colors"
                  title="Copy Tracking Number"
                >
                  {copied ? <Check className="h-3.5 w-3.5 text-emerald-600" /> : <Copy className="h-3.5 w-3.5" />}
                </button>
              </div>
            </div>

            <div className="flex justify-between items-center pt-2 border-t border-zinc-100">
              <span className="text-[10px] font-black uppercase text-zinc-450 tracking-wider">Estimated Delivery</span>
              <span className="text-xs font-black text-zinc-900">3 - 5 Business Days</span>
            </div>

            <div className="flex justify-between items-center pt-2 border-t border-zinc-100">
              <span className="text-[10px] font-black uppercase text-zinc-455 tracking-wider">Payment Mode</span>
              <span className="text-xs font-black text-zinc-900 uppercase">
                {paymentMethod === "cod" && "Cash on Delivery"}
                {paymentMethod === "card" && "Online card payment"}
                {paymentMethod === "googlepay" && "Google Pay"}
                {paymentMethod === "paypal" && "PayPal"}
                {paymentMethod === "applepay" && "Apple Pay"}
              </span>
            </div>
          </div>

          {/* Interactive Status Timeline */}
          <div className="mt-10 select-none max-w-lg mx-auto">
            <h3 className="text-[10px] font-black uppercase tracking-wider text-zinc-450 mb-6 flex items-center justify-center gap-1.5">
              <Truck className="h-4.5 w-4.5 text-zinc-400" /> Current Tracking Timeline
            </h3>
            
            <div className="relative flex items-center justify-between">
              {/* Progress track line */}
              <div className="absolute left-0 right-0 h-1 bg-zinc-100 z-0">
                <div className="h-full bg-emerald-500 w-1/4"></div>
              </div>

              {[
                { title: "Placed", active: true },
                { title: "Packed", active: false },
                { title: "Shipped", active: false },
                { title: "Transit", active: false },
                { title: "Delivered", active: false }
              ].map((node, index) => (
                <div key={index} className="flex flex-col items-center z-10 relative">
                  <div className={`h-6 w-6 rounded-full flex items-center justify-center border-2 text-[10px] font-black ${
                    node.active 
                      ? "bg-emerald-500 border-emerald-500 text-white" 
                      : "bg-white border-zinc-200 text-zinc-400"
                  }`}>
                    {index + 1}
                  </div>
                  <span className={`text-[9px] font-black uppercase mt-2 ${node.active ? "text-emerald-700" : "text-zinc-400"}`}>
                    {node.title}
                  </span>
                </div>
              ))}
            </div>
          </div>

          {/* Call-to-actions */}
          <div className="mt-10 flex flex-col sm:flex-row gap-4 max-w-md mx-auto">
            <Link href={getRelativePath(`/track/?code=${trackingNumber}`)} className="flex-1">
              <Button className="w-full bg-[#f51c50] hover:bg-[#f51c50]/90 text-white font-black text-xs uppercase tracking-widest py-6 rounded-xl shadow-xs">
                Track Live Shipment
              </Button>
            </Link>
            <Link href={getRelativePath("/catalog/")} className="flex-1">
              <Button variant="outline" className="w-full border-zinc-200 bg-white text-zinc-800 font-black text-xs uppercase tracking-widest py-6 rounded-xl hover:bg-zinc-50">
                Continue Shopping
              </Button>
            </Link>
          </div>

        </div>
      )}

    </div>
  );
}
