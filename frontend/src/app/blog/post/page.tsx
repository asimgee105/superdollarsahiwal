"use client";

import * as React from "react";
import Link from "next/link";
import { useSearchParams } from "next/navigation";
import { Calendar, Clock, ArrowLeft } from "lucide-react";
import { getRelativePath } from "@/lib/utils";

interface Post {
  id: number;
  title: string;
  slug: string;
  content: string;
  excerpt: string;
  image_url?: string;
  category?: string;
  read_time?: number;
  created_at: string;
}

function BlogPostContent() {
  const searchParams = useSearchParams();
  const slug = searchParams.get("slug") || "";

  const [post, setPost] = React.useState<Post | null>(null);
  const [loading, setLoading] = React.useState(true);

  const API_URL = process.env.NEXT_PUBLIC_API_URL || "http://localhost:8000";

  React.useEffect(() => {
    if (!slug) return;
    fetch(`${API_URL}/api/v1/posts/${slug}`)
      .then((res) => res.json())
      .then((data) => {
        setPost(data.data || data);
        setLoading(false);
      })
      .catch(() => setLoading(false));
  }, [slug, API_URL]);

  if (!slug) {
    return (
      <div className="text-center py-20 text-xs font-black uppercase tracking-widest text-[#f51c50]">
        No Post Selected.
      </div>
    );
  }

  return (
    <div className="max-w-3xl mx-auto space-y-6">
      
      {/* Back Link */}
      <Link href={getRelativePath("/blog")} className="inline-flex items-center gap-1.5 text-[11px] font-black uppercase tracking-wider text-zinc-450 hover:text-zinc-800 transition-colors">
        <ArrowLeft className="h-3.5 w-3.5" /> Back to Blog
      </Link>

      {loading ? (
        <div className="bg-white border border-zinc-150 rounded-2xl p-6 sm:p-8 space-y-4 shadow-3xs animate-pulse">
          <div className="h-6 bg-zinc-100 rounded-sm w-3/4"></div>
          <div className="h-4 bg-zinc-100 rounded-sm w-1/4"></div>
          <div className="aspect-video bg-zinc-100 rounded-xl w-full"></div>
          <div className="space-y-2">
            <div className="h-3 bg-zinc-100 rounded-sm w-full"></div>
            <div className="h-3 bg-zinc-100 rounded-sm w-5/6"></div>
          </div>
        </div>
      ) : !post ? (
        <div className="bg-white border border-zinc-150 rounded-2xl p-12 text-center shadow-3xs">
          <p className="text-zinc-500 font-semibold text-sm">Blog post not found.</p>
        </div>
      ) : (
        <div className="bg-white border border-zinc-150 rounded-2xl p-6 sm:p-8 shadow-3xs space-y-6">
          
          {/* Header info */}
          <div className="space-y-3 pb-5 border-b border-zinc-100">
            <div className="flex items-center gap-2">
              <span className="bg-[#f51c50]/5 text-[#f51c50] text-[8px] font-black uppercase tracking-wider px-2 py-0.5 rounded-sm">
                {post.category || "Editorial"}
              </span>
            </div>
            <h1 className="text-xl sm:text-2xl font-black text-zinc-900 uppercase tracking-wide leading-tight">
              {post.title}
            </h1>
            <div className="flex items-center gap-4 text-[10px] text-zinc-450 font-bold">
              <span className="flex items-center gap-1"><Calendar className="h-3.5 w-3.5" /> {new Date(post.created_at).toLocaleDateString()}</span>
              <span className="flex items-center gap-1"><Clock className="h-3.5 w-3.5" /> {post.read_time || 5} min read</span>
            </div>
          </div>

          {/* Featured Image */}
          <div className="aspect-video bg-zinc-50 rounded-xl overflow-hidden">
            <img 
              src={post.image_url || "https://images.unsplash.com/photo-1483985988355-763728e1935b?q=80&w=600"} 
              alt={post.title}
              className="object-cover w-full h-full"
            />
          </div>

          {/* Post Content */}
          <div 
            className="prose max-w-none text-zinc-650 font-medium text-xs sm:text-sm leading-relaxed space-y-4 pt-2"
            dangerouslySetInnerHTML={{ __html: post.content }}
          />

        </div>
      )}

    </div>
  );
}

export default function BlogDetailsPage() {
  return (
    <main className="min-h-screen bg-zinc-50/50 py-12 px-4 sm:px-6 lg:px-8 font-sans select-none">
      <React.Suspense fallback={<div className="text-center py-20 text-xs font-black uppercase tracking-widest text-[#f51c50]">Loading Post Details...</div>}>
        <BlogPostContent />
      </React.Suspense>
    </main>
  );
}
