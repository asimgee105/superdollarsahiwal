"use client";

import * as React from "react";
import Link from "next/link";
import { BookOpen, Calendar, Clock, ArrowRight, ArrowLeft } from "lucide-react";
import { getRelativePath } from "@/lib/utils";

interface Post {
  id: number;
  title: string;
  slug: string;
  excerpt: string;
  image_url?: string;
  category?: string;
  read_time?: number;
  created_at: string;
}

export default function BlogListingPage() {
  const [posts, setPosts] = React.useState<Post[]>([]);
  const [loading, setLoading] = React.useState(true);

  const API_URL = process.env.NEXT_PUBLIC_API_URL || "http://localhost:8000";

  React.useEffect(() => {
    fetch(`${API_URL}/api/v1/posts`)
      .then((res) => res.json())
      .then((data) => {
        // Handle array response or wrap if nested
        const list = Array.isArray(data) ? data : (data.data || []);
        setPosts(list);
        setLoading(false);
      })
      .catch(() => setLoading(false));
  }, [API_URL]);

  return (
    <main className="min-h-screen bg-zinc-50/50 py-12 px-4 sm:px-6 lg:px-8 font-sans select-none">
      <div className="max-w-5xl mx-auto space-y-6">
        
        {/* Back Link */}
        <Link href={getRelativePath("/")} className="inline-flex items-center gap-1.5 text-[11px] font-black uppercase tracking-wider text-zinc-450 hover:text-zinc-800 transition-colors">
          <ArrowLeft className="h-3.5 w-3.5" /> Back to Storefront
        </Link>

        <div className="flex items-center gap-3 border-b border-zinc-150 pb-5">
          <div className="p-2.5 bg-[#f51c50]/5 rounded-xl text-[#f51c50]">
            <BookOpen className="h-5 w-5" />
          </div>
          <div>
            <span className="text-[10px] font-black uppercase tracking-widest text-[#f51c50]">Editorial</span>
            <h1 className="text-lg font-black text-zinc-900 uppercase tracking-wider mt-0.5">AURA Fashion Blog</h1>
          </div>
        </div>

        {loading ? (
          <div className="grid grid-cols-1 md:grid-cols-3 gap-6 pt-4">
            {[1, 2, 3].map((n) => (
              <div key={n} className="bg-white border border-zinc-150 rounded-2xl p-5 space-y-4 shadow-3xs animate-pulse">
                <div className="aspect-video bg-zinc-100 rounded-xl w-full"></div>
                <div className="h-4 bg-zinc-100 rounded-sm w-3/4"></div>
                <div className="h-3 bg-zinc-100 rounded-sm w-full"></div>
                <div className="h-3 bg-zinc-100 rounded-sm w-5/6"></div>
              </div>
            ))}
          </div>
        ) : posts.length === 0 ? (
          <div className="bg-white border border-zinc-150 rounded-2xl p-12 text-center shadow-3xs">
            <p className="text-zinc-500 font-semibold text-sm">No blog posts available at the moment.</p>
          </div>
        ) : (
          <div className="grid grid-cols-1 md:grid-cols-3 gap-6 pt-4">
            {posts.map((post) => (
              <div key={post.id} className="bg-white border border-zinc-150 rounded-2xl overflow-hidden shadow-3xs flex flex-col justify-between group hover:border-[#f51c50]/40 transition-colors">
                <div>
                  <div className="aspect-video bg-zinc-50 relative overflow-hidden">
                    <img 
                      src={post.image_url || "https://images.unsplash.com/photo-1483985988355-763728e1935b?q=80&w=400"} 
                      alt={post.title}
                      className="object-cover w-full h-full group-hover:scale-105 transition-transform duration-500"
                    />
                    {post.category && (
                      <span className="absolute top-3 left-3 bg-[#f51c50] text-white text-[8px] font-black uppercase tracking-wider px-2 py-0.5 rounded-sm">
                        {post.category}
                      </span>
                    )}
                  </div>
                  <div className="p-5 space-y-2.5">
                    <div className="flex items-center gap-3 text-[10px] text-zinc-450 font-bold">
                      <span className="flex items-center gap-1"><Calendar className="h-3 w-3" /> {new Date(post.created_at).toLocaleDateString()}</span>
                      <span className="flex items-center gap-1"><Clock className="h-3 w-3" /> {post.read_time || 5} min read</span>
                    </div>
                    <h2 className="text-sm font-black text-zinc-850 uppercase tracking-wide group-hover:text-[#f51c50] transition-colors leading-tight line-clamp-2">
                      {post.title}
                    </h2>
                    <p className="text-xs text-zinc-500 font-semibold leading-relaxed line-clamp-3">
                      {post.excerpt}
                    </p>
                  </div>
                </div>
                <div className="p-5 pt-0">
                  <Link 
                    href={getRelativePath(`/blog/post?slug=${post.slug}`)} 
                    className="inline-flex items-center gap-1 text-[10px] font-black uppercase tracking-wider text-[#f51c50] hover:text-[#e01445]"
                  >
                    Read Article <ArrowRight className="h-3 w-3" />
                  </Link>
                </div>
              </div>
            ))}
          </div>
        )}

      </div>
    </main>
  );
}
