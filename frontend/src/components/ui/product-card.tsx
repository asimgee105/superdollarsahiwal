"use client";

import * as React from "react";
import Image from "next/image";
import { Card, CardContent, CardFooter } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";

export interface ProductCardProps {
  id: string;
  name: string;
  brand: string;
  price: number;
  compareAtPrice?: number;
  images: string[];
  status?: string;
  onAddToCart?: (id: string) => void;
}

export function ProductCard({
  id,
  name,
  brand,
  price,
  compareAtPrice,
  images,
  status,
  onAddToCart,
}: ProductCardProps) {
  const hasDiscount = compareAtPrice && compareAtPrice > price;
  const discountPercentage = hasDiscount
    ? Math.round(((compareAtPrice! - price) / compareAtPrice!) * 100)
    : 0;

  return (
    <Card className="group relative overflow-hidden rounded-lg border border-border bg-card transition-all duration-300 hover:shadow-lg">
      {/* Badge container */}
      <div className="absolute top-3 left-3 z-10 flex flex-col gap-1">
        {hasDiscount && (
          <Badge variant="destructive" className="bg-destructive px-2 py-0.5 text-xs font-semibold">
            {discountPercentage}% OFF
          </Badge>
        )}
        {status && status !== "active" && (
          <Badge className="bg-primary text-primary-foreground px-2 py-0.5 text-xs font-semibold uppercase">
            {status}
          </Badge>
        )}
      </div>

      {/* Image container */}
      <div className="relative aspect-3/4 w-full overflow-hidden bg-muted">
        {images && images.length > 0 ? (
          <>
            <Image
              src={images[0]}
              alt={name}
              fill
              sizes="(max-width: 768px) 50vw, (max-width: 1200px) 33vw, 25vw"
              className="object-cover transition-transform duration-500 group-hover:scale-105"
            />
            {images.length > 1 && (
              <Image
                src={images[1]}
                alt={name}
                fill
                sizes="(max-width: 768px) 50vw, (max-width: 1200px) 33vw, 25vw"
                className="object-cover opacity-0 transition-opacity duration-500 group-hover:opacity-100"
              />
            )}
          </>
        ) : (
          <div className="flex h-full w-full items-center justify-center text-muted-foreground text-xs">
            No Image
          </div>
        )}

        {/* Hover Quick Actions */}
        <div className="absolute inset-x-0 bottom-0 translate-y-full p-4 transition-transform duration-300 group-hover:translate-y-0">
          <Button
            onClick={() => onAddToCart?.(id)}
            className="w-full bg-background/95 hover:bg-background text-foreground backdrop-blur-sm border border-border shadow-sm text-xs font-medium uppercase tracking-wider transition-colors duration-200"
          >
            Add To Cart
          </Button>
        </div>
      </div>

      {/* Product Details */}
      <CardContent className="p-4">
        <p className="text-[10px] uppercase tracking-widest text-muted-foreground font-semibold">
          {brand}
        </p>
        <h3 className="mt-1 line-clamp-1 text-sm font-medium text-foreground tracking-tight">
          {name}
        </h3>
      </CardContent>

      {/* Pricing Footer */}
      <CardFooter className="flex items-baseline gap-2 px-4 pb-4 pt-0">
        <span className="text-sm font-bold text-foreground">${price.toFixed(2)}</span>
        {hasDiscount && (
          <span className="text-xs text-muted-foreground line-through">
            ${compareAtPrice!.toFixed(2)}
          </span>
        )}
      </CardFooter>
    </Card>
  );
}
