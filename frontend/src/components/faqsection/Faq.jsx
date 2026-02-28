import Accordion from "@/components/faqsection/Accordion";

export default function Home() {
  return (
    <main className="min-h-screen px-4 py-14">
      <div className="text-center mb-12">
        <h1 className="text-3xl md:text-5xl font-bold text-white">
          Lead Marketplace Support
        </h1>

        <p className="mt-4 text-gray-400 text-sm md:text-lg">
          Quickly find answers regarding lead buying, selling, and verification.
        </p>
      </div>

      <Accordion />
    </main>
  );
}
