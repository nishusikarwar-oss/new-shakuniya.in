
import { notFound } from "next/navigation";
import Navbar from "@/components/Navbar";
import Footer from "@/components/Footer";
import * as Icons from "lucide-react";
// Icon mapping
const IconMap = {
  Zap: Icons.Zap,
  MessageSquare: Icons.MessageSquare,
  Users: Icons.Users,
  ShieldCheck: Icons.ShieldCheck,
  Globe: Icons.Globe,
  Mail: Icons.Mail,
  Phone: Icons.Phone,
  Bot: Icons.Bot,
  Video: Icons.Video,
  Lock: Icons.Lock,
}; 

async function getProductData(slug) {
  try {
    console.log('Fetching product with slug:', slug);
    
    // First, get all products to find the one with matching slug
    const productsResponse = await fetch(`${process.env.NEXT_PUBLIC_API_URL}/products`, {
      // next: { revalidate: 3600 } // Cache for 1 hour
    });
    
    if (!productsResponse.ok) {
      throw new Error(`Failed to fetch products: ${productsResponse.status}`);
    }
    
    const responseData = await productsResponse.json();
    console.log('Products API Response:', responseData);
    
    // Extract products array from paginated response
    let productsArray = [];
    
    if (responseData.success === true && responseData.data) {
      // Laravel pagination response
      if (responseData.data.data && Array.isArray(responseData.data.data)) {
        productsArray = responseData.data.data; // Products are in data.data
      } else if (Array.isArray(responseData.data)) {
        productsArray = responseData.data;
      }
    } else if (Array.isArray(responseData)) {
      productsArray = responseData;
    } else if (responseData.data && Array.isArray(responseData.data)) {
      productsArray = responseData.data;
    }
    
    console.log('Extracted Products Array:', productsArray);
    
    // Find the product with matching slug
    const product = productsArray.find(p => p.slug === slug);
    
    if (!product) {
      console.log('Product not found for slug:', slug);
      return null;
    }
    
    console.log('Found Product:', product);

    // Fetch product features
    const featuresResponse = await fetch(
  `${process.env.NEXT_PUBLIC_API_URL}/products/${product.id}/features`,
  { next: { revalidate: 3600 } }
);
    
    let featuresData = [];
    if (featuresResponse.ok) {
      const featuresJson = await featuresResponse.json();
      console.log('Features API Response: ==>', featuresJson);
      // Handle different feature response formats
      if (featuresJson.success === true && featuresJson.data) {
        if (Array.isArray(featuresJson.data)) {
          featuresData = featuresJson.data;
        } else if (featuresJson.data.data && Array.isArray(featuresJson.data.data)) {
          featuresData = featuresJson.data.data;
        }
      } else if (Array.isArray(featuresJson)) {
        featuresData = featuresJson;
      }
    }

    // Fetch product images
    const imagesResponse = await fetch(
      `${process.env.NEXT_PUBLIC_API_URL}/product-images?product_id=${product.id}`,
      { next: { revalidate: 3600 } }
    );
    
    let imagesData = [];
    if (imagesResponse.ok) {
      const imagesJson = await imagesResponse.json();
      
      if (imagesJson.success === true && imagesJson.data) {
        if (Array.isArray(imagesJson.data)) {
          imagesData = imagesJson.data;
        } else if (imagesJson.data.data && Array.isArray(imagesJson.data.data)) {
          imagesData = imagesJson.data.data;
        }
      } else if (Array.isArray(imagesJson)) {
        imagesData = imagesJson;
      }
    }

    // Fetch pricing tiers
    const pricingResponse = await fetch(
      `${process.env.NEXT_PUBLIC_API_URL}/pricing-tiers?product_id=${product.id}`,
      { next: { revalidate: 3600 } }
    );
    
    let pricingData = [];
    if (pricingResponse.ok) {
      const pricingJson = await pricingResponse.json();
      
      if (pricingJson.success === true && pricingJson.data) {
        if (Array.isArray(pricingJson.data)) {
          pricingData = pricingJson.data;
        } else if (pricingJson.data.data && Array.isArray(pricingJson.data.data)) {
          pricingData = pricingJson.data.data;
        }
      } else if (Array.isArray(pricingJson)) {
        pricingData = pricingJson;
      }
    }

    // Fetch tier features for each pricing tier
    const tiersWithFeatures = await Promise.all(
      pricingData.map(async (tier) => {
        try {
          const tierFeaturesResponse = await fetch(
            `${process.env.NEXT_PUBLIC_API_URL}/tier-features?tier_id=${tier.id}`,
            { next: { revalidate: 3600 } }
          );
          
          let tierFeaturesData = [];
          if (tierFeaturesResponse.ok) {
            const tierFeaturesJson = await tierFeaturesResponse.json();
            
            if (tierFeaturesJson.success === true && tierFeaturesJson.data) {
              if (Array.isArray(tierFeaturesJson.data)) {
                tierFeaturesData = tierFeaturesJson.data;
              } else if (tierFeaturesJson.data.data && Array.isArray(tierFeaturesJson.data.data)) {
                tierFeaturesData = tierFeaturesJson.data.data;
              }
            } else if (Array.isArray(tierFeaturesJson)) {
              tierFeaturesData = tierFeaturesJson;
            }
          }
          
          return { ...tier, features: tierFeaturesData };
        } catch (error) {
          console.error(`Error fetching features for tier ${tier.id}:`, error);
          return { ...tier, features: [] };
        }
      })
    );

    // Fetch related products
    const relatedResponse = await fetch(
      `${process.env.NEXT_PUBLIC_API_URL}/related-products?product_id=${product.id}`,
      { next: { revalidate: 3600 } }
    );
    
    let relatedData = [];
    if (relatedResponse.ok) {
      const relatedJson = await relatedResponse.json();
      
      if (relatedJson.success === true && relatedJson.data) {
        if (Array.isArray(relatedJson.data)) {
          relatedData = relatedJson.data;
        } else if (relatedJson.data.data && Array.isArray(relatedJson.data.data)) {
          relatedData = relatedJson.data.data;
        }
      } else if (Array.isArray(relatedJson)) {
        relatedData = relatedJson;
      }
    }

    // Map features with icons
    const featuresWithIcons = featuresData.map(feature => ({
      ...feature,
      icon: IconMap[feature.icon_name] || Icons.MessageSquare
    }));

    return {
      id: product.id,
      slug: product.slug,
      name: product.title || 'Untitled Product',
      title: product.title || 'Untitled Product',
      short_description: product.short_description || '',
      full_description: product.full_description || product.description || '',
      description: product.full_description || product.description || product.short_description || '',
      video_url: product.video_url,
      video_text: product.video_text,
      image: product.image_url || product.image || '',
      meta_title: product.meta_title,
      meta_description: product.meta_description,
      meta_keywords: product.meta_keywords,
      features: featuresWithIcons,
      pricingTiers: tiersWithFeatures,
      relatedProducts: relatedData
    };
    
  } catch (error) {
    console.error('Error fetching product data:', error);
    return null;
  }
}

export async function generateMetadata({ params }) {
  const { slug } = await params;
  const product = await getProductData(slug);
  
  if (!product) return {};
  
  const mediaBaseUrl = process.env.NEXT_PUBLIC_API_URL?.replace('/api', '') || 'NEXT_PUBLIC_API_URL';
  const imageUrl = product.image ? (product.image.startsWith('http') ? product.image : `${mediaBaseUrl}/${product.image}`) : null;

  return {
    title: product.meta_title || product.title,
    description: product.meta_description || product.short_description,
    keywords: product.meta_keywords,
    openGraph: {
      title: product.og_title || product.meta_title || product.title,
      description: product.og_description || product.meta_description || product.short_description,
      image: imageUrl ? [{ url: imageUrl }] : [],
    },
    twitter: {
      card: 'summary_large_image',
      title: product.twitter_title || product.meta_title || product.title,
      description: product.twitter_description || product.meta_description || product.short_description,
      images: imageUrl ? [imageUrl] : [],
    },
    alternates: {
      canonical: product.canonical_url,
    }
  };
}

export async function generateStaticParams() {
  try {
    const response = await fetch(`${process.env.NEXT_PUBLIC_API_URL}/products`);
    const responseData = await response.json();
    
    // Extract products array from paginated response
    let productsArray = [];
    
    if (responseData.success === true && responseData.data) {
      if (responseData.data.data && Array.isArray(responseData.data.data)) {
        productsArray = responseData.data.data;
      } else if (Array.isArray(responseData.data)) {
        productsArray = responseData.data;
      }
    } else if (Array.isArray(responseData)) {
      productsArray = responseData;
    }
    
    return productsArray.map((product) => ({
      slug: product.slug,
    }));
  } catch (error) {
    console.error('Error generating static params:', error);
    return [];
  }
}

export default async function ProductPage({ params }) {
  const { slug } = await params;
  const product = await getProductData(slug);

  if (!product) return notFound();

  // Get the first pricing tier for display
  const firstTier = product.pricingTiers && Array.isArray(product.pricingTiers) && product.pricingTiers.length > 0 
    ? product.pricingTiers[0] 
    : null;

  // Helper to render video
  const renderVideo = (url) => {
    if (!url) return null;

    // YouTube
    const youtubeRegex = /(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/;
    const youtubeMatch = url.match(youtubeRegex);
    if (youtubeMatch) {
      return (
        <iframe
          src={`https://www.youtube.com/embed/${youtubeMatch[1]}`}
          className="w-full h-full"
          allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
          allowFullScreen
        />
      );
    }

    // Vimeo
    const vimeoRegex = /vimeo\.com\/(?:video\/)?(\d+)/;
    const vimeoMatch = url.match(vimeoRegex);
    if (vimeoMatch) {
      return (
        <iframe
          src={`https://player.vimeo.com/video/${vimeoMatch[1]}`}
          className="w-full h-full"
          allow="autoplay; fullscreen; picture-in-picture"
          allowFullScreen
        />
      );
    }

    // Direct MP4
    return (
      <video
        src={url}
        autoPlay
        loop
        muted
        playsInline
        controls
        className="w-full h-full object-cover"
      />
    );
  };

  // Base URL for images
  const mediaBaseUrl = process.env.NEXT_PUBLIC_API_URL?.replace('/api', '') || 'NEXT_PUBLIC_API_URL';

  // Helper to render image
  const renderImage = (path, alt) => {
    if (!path) return <div className="text-gray-500">No image available</div>;
    const src = path.startsWith('http') ? path : `${mediaBaseUrl}/${path}`;
    return (
      <img
        src={`http://127.0.0.1:8000/storage/${product.image}` || src}
        alt={alt}
        className="w-full h-full object-cover transition-transform duration-1000 group-hover:scale-105"
      />
    );
  };
  
  return (
    <>
      <Navbar />

      <div className="min-h-screen bg-gradient-to-br from-[#0a0517] via-[#020617] to-[#0f172a] text-white selection:bg-purple-500/30">
        {/* Hero Section */}
        <section className="relative pt-24 pb-32">
          <div className="container mx-auto px-6 relative z-10">
            <div className="grid lg:grid-cols-2 gap-16 items-center">
              <div>
                <h1 className="text-7xl md:text-8xl font-black mb-6 tracking-tight leading-tight">
                  <span className="bg-gradient-to-r from-purple-500 via-pink-500 to-purple-400 bg-clip-text text-transparent">
                    {product.title}
                  </span>
                </h1>

                {/* Short Description */}
                <p className="text-base md:text-lg text-gray-300 mb-4 leading-relaxed max-w-lg font-light">
                  {product.short_description}
                </p>
                
                {/* Full Description */}
                {product.full_description && product.full_description !== product.short_description && (
                  <p className="text-base md:text-lg text-gray-300 mb-10 leading-relaxed max-w-lg font-light">
                    {product.full_description}
                  </p>
                )}

                {/* Pricing Tier Display */}
                {firstTier && (
                  <div className="mb-10">
                    <div className="bg-gradient-to-br from-purple-950/30 to-blue-950/20 backdrop-blur-md border border-purple-500/30 p-6 rounded-2xl inline-block min-w-[300px] shadow-2xl shadow-purple-900/20">
                      <p className="text-[11px] font-bold uppercase tracking-widest text-purple-300/70 mb-3">
                        {firstTier.tier_name} - Starting From
                      </p>
                      <p className="text-4xl md:text-5xl font-bold text-white tracking-tight bg-gradient-to-r from-white to-purple-200 bg-clip-text text-transparent">
                        ${firstTier.price_usd} / ₹{firstTier.price_inr}
                      </p>
                      <p className="text-sm text-purple-300/70 mt-2">
                        {firstTier.billing_period === 'monthly' ? 'per month' : 'one-time'}
                      </p>
                    </div>
                  </div>
                )}
              </div>

              {/* Product Image */}
              <div className="relative h-full flex items-center justify-center lg:justify-end">
                <div className="relative w-full h-96 bg-gradient-to-br from-purple-950/40 to-blue-950/40 border border-purple-500/20 rounded-3xl shadow-2xl overflow-hidden flex items-center justify-center group">
                  {renderImage(product.image, product.title)}
                </div>
              </div>
            </div>
          </div>
        </section>

        {/* Features Section */}
        <section className="py-32 relative border-t border-purple-500/10 bg-gradient-to-b from-[#020617] to-[#0a0517]">
          <div className="container mx-auto px-6 grid lg:grid-cols-2 gap-16 items-center">
            <div className="relative rounded-3xl overflow-hidden border border-purple-500/30 shadow-2xl bg-black aspect-video">
              {product.video_url ? (
                renderVideo(product.video_url)
              ) : (
                <div className="w-full h-full flex items-center justify-center text-gray-500">
                  No video available
                </div>
              )}
            </div>

            <div className="space-y-5">
              {/* Video Description/Text */}
              {product.video_text && (
                <div className="mb-8 p-6 rounded-2xl bg-gradient-to-br from-purple-500/10 to-blue-500/10 border border-purple-500/20 shadow-xl">
                  <h3 className="text-xl font-bold mb-4 text-white">Video Details</h3>
                  <p className="text-gray-300 leading-relaxed italic">
                    {product.video_text}
                  </p>
                </div>
              )}

              {product.features && Array.isArray(product.features) && product.features.length > 0 ? (
                product.features.map((feature, index) => {
                  const Icon = feature.icon || Icons.MessageSquare;

                  return (
                    <div
                      key={index}
                      className="p-6 md:p-8 rounded-2xl bg-purple-950/20 border border-purple-500/20"
                    >
                      <div className="flex gap-5 items-start">
                        <div className="p-3 rounded-xl bg-purple-950/50 text-purple-300">
                          <Icon size={32} strokeWidth={1.5} />
                        </div>
                        <div>
                          <h3 className="text-lg md:text-xl font-bold mb-2 text-gray-100">
                            {feature.title}
                          </h3>
                          <p className="text-gray-400">
                            {feature.description}
                          </p>
                        
                        </div>
                      </div>
                    </div>
                  );
                })
              ) : (
                <div className="text-center text-gray-400 py-10">
                  No features available for this product
                </div>
              )}
            </div>
          </div>
        </section>

        {/* Pricing Tiers Section */}
        {product.pricingTiers && Array.isArray(product.pricingTiers) && product.pricingTiers.length > 0 && (
          <section className="py-20 border-t border-purple-500/10">
            <div className="container mx-auto px-6">
              <h2 className="text-3xl md:text-4xl font-bold text-center mb-12">
                <span className="bg-gradient-to-r from-purple-400 to-pink-400 bg-clip-text text-transparent">
                  Pricing Plans
                </span>
              </h2>
              <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                {product.pricingTiers.map((tier) => (
                  <div
                    key={tier.id}
                    className={`p-6 rounded-2xl bg-purple-950/20 border ${
                      tier.is_popular ? 'border-purple-400' : 'border-purple-500/20'
                    } hover:border-purple-500/40 transition-all relative`}
                  >
                    {/* Popular Badge */}
                    {tier.is_popular && (
                      <div className="absolute -top-3 left-1/2 transform -translate-x-1/2">
                        <span className="bg-purple-500 text-white px-3 py-1 rounded-full text-xs font-bold">
                          Most Popular
                        </span>
                      </div>
                    )}
                    
                    {/* Tier Name */}
                    <h3 className="text-xl font-bold mb-2">{tier.tier_name}</h3>
                    
                    {/* Prices - USD and INR both */}
                    <div className="mb-4">
                      <p className="text-3xl font-bold text-purple-400">
                        ${tier.price_usd}
                        <span className="text-sm text-gray-400 font-normal ml-2 capitalize">
                          / {tier.billing_period}
                        </span>
                      </p>
                      <p className="text-lg text-gray-400">
                        ₹{tier.price_inr}
                      </p>
                    </div>
                    
                    {/* Display Order (optional) */}
                    {tier.display_order && (
                      <p className="text-xs text-purple-300/50 mb-4">
                        Plan #{tier.display_order}
                      </p>
                    )}
                    
                    {/* Tier Features */}
                    {tier.features && Array.isArray(tier.features) && tier.features.length > 0 && (
                      <ul className="space-y-2 mt-4">
                        {tier.features.map((feature, idx) => {
                          // Feature description ko properly extract karo
                          const featureText = feature.feature_description || feature.name || feature.description || 'Feature';
                          return (
                            <li key={idx} className="text-gray-300 flex items-center gap-2">
                              <span className="w-1.5 h-1.5 rounded-full bg-purple-500" />
                              {featureText}
                            </li>
                          );
                        })}
                      </ul>
                    )}
                  </div>
                ))}
              </div>
            </div>
          </section>
        )}
      </div>

      <Footer />
    </>
  );
}