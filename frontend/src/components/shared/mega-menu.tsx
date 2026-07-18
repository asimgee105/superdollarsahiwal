import * as React from "react";
import Link from "next/link";

interface MegaMenuCategory {
  title: string;
  items: { name: string; href: string }[];
}

export interface MegaMenuProps {
  isOpen: boolean;
  categories: MegaMenuCategory[];
  promoImage?: string;
  promoTitle?: string;
  promoText?: string;
}

export function MegaMenu({
  isOpen,
  categories,
  promoImage,
  promoTitle,
  promoText,
}: MegaMenuProps) {
  if (!isOpen) return null;

  return (
    <div className="absolute left-0 right-0 top-full z-40 border-b border-border bg-background shadow-xl transition-all duration-200">
      <div className="mx-auto max-w-7xl px-8 py-8">
        <div className="grid grid-cols-5 gap-8">
          {/* Main Category Columns */}
          <div className="col-span-4 grid grid-cols-4 gap-6">
            {categories.map((cat, idx) => (
              <div key={idx}>
                <h4 className="font-heading text-xs font-bold uppercase tracking-widest text-foreground">
                  {cat.title}
                </h4>
                <ul className="mt-4 space-y-2">
                  {cat.items.map((item, itemIdx) => (
                    <li key={itemIdx}>
                      <Link
                        href={item.href}
                        className="text-xs text-muted-foreground transition-colors hover:text-foreground"
                      >
                        {item.name}
                      </Link>
                    </li>
                  ))}
                </ul>
              </div>
            ))}
          </div>

          {/* Promotion Card Section */}
          <div className="col-span-1 border-l border-border pl-8">
            <div className="group relative aspect-4/5 overflow-hidden rounded-lg bg-muted">
              {promoImage ? (
                // eslint-disable-next-line @next/next/no-img-element
                <img
                  src={promoImage}
                  alt={promoTitle || "Promo"}
                  className="h-full w-full object-cover transition-transform duration-500 group-hover:scale-102"
                />
              ) : (
                <div className="flex h-full w-full items-center justify-center text-[10px] uppercase tracking-wider text-muted-foreground">
                  Promo Spot
                </div>
              )}
              <div className="absolute inset-0 bg-gradient-to-t from-black/80 via-black/30 to-transparent p-4 flex flex-col justify-end text-white">
                {promoTitle && <h5 className="font-heading text-sm font-bold">{promoTitle}</h5>}
                {promoText && <p className="mt-1 text-[10px] text-zinc-300">{promoText}</p>}
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}
