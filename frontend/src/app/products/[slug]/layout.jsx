export async function generateMetadata({ params }) {
  const { slug } = params;
  try {
    const productsResponse = await fetch(`${process.env.NEXT_PUBLIC_API_URL}/products`);
    const responseData = await productsResponse.json();
    let productsArray = [];
    if (responseData.success === true && responseData.data) {
      if (responseData.data.data && Array.isArray(responseData.data.data)) {
        productsArray = responseData.data.data;
      } else if (Array.isArray(responseData.data)) {
        productsArray = responseData.data;
      }
    }
    const product = productsArray.find(p => p.slug === slug);
    if (product) {
      return {
        title: `Shakuniya Solutions | ${product.name}`,
        description: product.description || `Learn more about ${product.name}`,
      };
    }
  } catch (error) {
    console.error("Error generating metadata for product:", error);
  }
  return {
    title: "Shakuniya Solutions | Product",
  };
}

export default function ProductLayout({ children }) {
  return <>{children}</>;
}
