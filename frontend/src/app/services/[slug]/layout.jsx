export async function generateMetadata({ params }) {
  const { slug } = params;
  try {
    const response = await fetch(`${process.env.NEXT_PUBLIC_API_URL}/services/${slug}`);
    const data = await response.json();
    if (data.success && data.data) {
      return {
        title: `Shakuniya Solutions | ${data.data.title}`,
        description: data.data.description || `Learn more about our ${data.data.title} services.`,
      };
    }
  } catch (error) {
    console.error("Error generating metadata for service:", error);
  }
  return {
    title: "Shakuniya Solutions | Service",
  };
}

export default function ServiceLayout({ children }) {
  return <>{children}</>;
}
