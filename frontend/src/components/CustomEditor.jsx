
"use client";

import { useState } from "react";
import dynamic from "next/dynamic";

const EditorContent = dynamic(
  () => import("./EditorContent").then((mod) => ({ default: mod.EditorContent })),
  {
    ssr: false,
    loading: () => (
      <div className="bg-[#111118] border border-white/10 p-6 rounded-2xl shadow-xl h-96 flex items-center justify-center">
        Loading editor...
      </div>
    ),
  }
);

export default function CustomEditor({ value, onChange }) {
  return <EditorContent content={value} setContent={onChange} />;
}
