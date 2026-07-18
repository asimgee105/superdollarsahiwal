import type { Metadata } from "next";
import "./globals.css";
import { Header } from "@/components/shared/header";
import { Footer } from "@/components/shared/footer";
import { TooltipProvider } from "@/components/ui/tooltip";
import { Toaster } from "@/components/ui/sonner";

export const metadata: Metadata = {
  title: "AURA - Modern Premium Fashion E-Commerce",
  description: "Discover curated luxury streetwear, timeless tailoring, and dynamic accessories.",
  manifest: "/manifest.json",
};

export default function RootLayout({
  children,
}: Readonly<{
  children: React.ReactNode;
}>) {
  return (
    <html lang="en" className="h-full antialiased" suppressHydrationWarning>
      <body className="min-h-full flex flex-col bg-background text-foreground selection:bg-muted selection:text-foreground">
        <TooltipProvider>
          <Header />
          <main className="flex-grow">{children}</main>
          <Footer />
          <Toaster position="bottom-right" closeButton richColors />
        </TooltipProvider>
      </body>
    </html>
  );
}
