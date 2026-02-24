"use client";
const ServiceFeatures = ({ title, subtitle, features }) => {
  return (
    <section className="py-20 lg:py-28 relative">
      <div className="container mx-auto px-4 lg:px-8">
        
        {/* Heading */}
        <div className="text-center mb-16">
          <h2 className="text-3xl md:text-4xl lg:text-5xl font-bold mb-4">
            {title.split(" ").map((word, i) => (
              <span
                key={i}
                className={
                  word.toLowerCase() === "services" ||
                  word.toLowerCase() === "services."
                    ? "gradient-text"
                    : ""
                }
              >
                {word}{" "}
              </span>
            ))}
          </h2>

          <p className="text-muted-foreground text-lg max-w-2xl mx-auto">
            {subtitle}
          </p>
        </div>

        {/* Features Grid */}
        <div className="max-w-[1300px] mx-auto grid md:grid-cols-2 lg:grid-cols-4 gap-6">
          {features?.map((feature, index) => {
            const IconComponent = feature.icon;
            return (
              <div
                key={index}
                className="glass-card p-6 rounded-2xl border-1 border-purple-500 hover:border-none  hover:bg-white/10 transition-all duration-300  group hover:-translate-y-2"
              >
                <div className="w-14 h-14  mb-5 ">
                  {feature.image ? (
                    <img
                      src={feature.image}/>
                  ) : IconComponent ? (
                    <IconComponent size={28}  />
                  ) : null}
                </div>

                <h3 className="text-xl font-bold mb-3 text-foreground">
                  {feature.title}
                </h3>

                <p className="text-muted-foreground text-sm leading-relaxed">
                  {feature.description}
                </p>
              </div>
            );
          })}
        </div>

      </div>
    </section>
  );
};

export default ServiceFeatures;
