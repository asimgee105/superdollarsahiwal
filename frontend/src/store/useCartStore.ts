import { create } from "zustand";
import { persist } from "zustand/middleware";

export interface CartItem {
  id: string; // unique cart item id (productId-size-color)
  productId: number;
  title: string;
  brand: string;
  image: string;
  price: number;
  size: string | null;
  color: string | null;
  quantity: number;
}

export interface WishlistItem {
  productId: number;
  title: string;
  brand: string;
  image: string;
  price: number;
}

interface CartStore {
  cart: CartItem[];
  wishlist: WishlistItem[];
  addToCart: (item: Omit<CartItem, "quantity" | "id">) => void;
  removeFromCart: (itemId: string) => void;
  toggleWishlist: (item: WishlistItem) => void;
  clearCart: () => void;
}

export const useCartStore = create<CartStore>()(
  persist(
    (set) => ({
      cart: [],
      wishlist: [],

      addToCart: (item) => {
        set((state) => {
          const itemId = `${item.productId}-${item.size || "none"}-${item.color || "none"}`;
          const existingIndex = state.cart.findIndex((i) => i.id === itemId);

          if (existingIndex > -1) {
            const newCart = [...state.cart];
            newCart[existingIndex].quantity += 1;
            return { cart: newCart };
          }

          return {
            cart: [...state.cart, { ...item, id: itemId, quantity: 1 }],
          };
        });
      },

      removeFromCart: (itemId) => {
        set((state) => ({
          cart: state.cart.filter((i) => i.id !== itemId),
        }));
      },

      toggleWishlist: (item) => {
        set((state) => {
          const exists = state.wishlist.some((w) => w.productId === item.productId);
          if (exists) {
            return {
              wishlist: state.wishlist.filter((w) => w.productId !== item.productId),
            };
          }
          return {
            wishlist: [...state.wishlist, item],
          };
        });
      },

      clearCart: () => set({ cart: [] }),
    }),
    {
      name: "aura-cart-storage",
    }
  )
);
