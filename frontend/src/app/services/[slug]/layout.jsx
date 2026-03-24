export async function generateMetadata({ params }) {
 
const resolvedParams = await params;
const slug = resolvedParams.slug;
  try {
    const serviceResponse = await fetch(`${process.env.NEXT_PUBLIC_API_URL}/services/${slug}`);
    const responseText = await serviceResponse.text();
      let data;
      console.log("Raw API Response Layouty:", responseText);
      try {
        data = JSON.parse(responseText);        
      } catch (error) {
        console.error("Response is not valid JSON:", responseText);
      }
    if (data.success ) {
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
