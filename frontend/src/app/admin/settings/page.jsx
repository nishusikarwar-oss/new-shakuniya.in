// "use client";

// import { useState } from "react";
// import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
// import { Button } from "@/components/ui/button";
// import { Input } from "@/components/ui/input";
// import { Label } from "@/components/ui/label";
// import { Textarea } from "@/components/ui/textarea";
// import { Switch } from "@/components/ui/switch";
// import { Separator } from "@/components/ui/separator";
// import { useAuth } from "@/hooks/useAuth";
// import { useToast } from "@/hooks/use-toast";

// const Settings = () => {
//   const { user } = useAuth();
//   const { toast } = useToast();
//   const [isLoading, setIsLoading] = useState(false);

//   const handleSave = () => {
//     setIsLoading(true);
//     setTimeout(() => {
//       setIsLoading(false);
//       toast({
//         title: "Settings Saved",
//         description: "Your settings have been updated successfully.",
//       });
//     }, 1000);
//   };

//   return (
//     <div className="space-y-6 text-gray-200">

      {/* Header */}
      {/* <div>
        <h1 className="text-2xl md:text-3xl font-bold">
          Settings
        </h1>
        <p className="text-gray-400 mt-1">
          Manage your account and site settings
        </p>
      </div>

      <div className="grid gap-6"> */}

        {/* Profile Settings */}
        {/* <Card className="bg-slate-950 border border-white/10">
          <CardHeader>
            <CardTitle className="text-lg">
              Profile Settings
            </CardTitle>
          </CardHeader>

          <CardContent className="space-y-4">
            <div className="flex items-center gap-4">
              <div className="w-16 h-16 rounded-full bg-gradient-to-r from-purple-500 to-cyan-500 flex items-center justify-center">
                <span className="text-white text-xl font-bold">
                  {user?.email?.charAt(0).toUpperCase() || "A"}
                </span>
              </div>

              <Button
                variant="outline"
                size="sm"
                className="border-white/10 text-gray-300 hover:bg-white/5"
              >
                Change Avatar
              </Button>
            </div>

            <Separator className="bg-white/10" />

            <div className="grid gap-4 sm:grid-cols-2">
              <div className="space-y-2">
                <Label>Full Name</Label>
                <Input
                  defaultValue={user?.user_metadata?.full_name || ""}
                  className="bg-slate-900 border-white/10 text-gray-200"
                />
              </div>

              <div className="space-y-2">
                <Label>Email</Label>
                <Input
                  type="email"
                  defaultValue={user?.email || ""}
                  disabled
                  className="bg-slate-900 border-white/10 text-gray-400"
                />
              </div>
            </div>
          </CardContent>
        </Card> */}

        {/* Site Settings */}
        {/* <Card className="bg-slate-950 border border-white/10">
          <CardHeader>
            <CardTitle className="text-lg">
              Site Settings
            </CardTitle>
          </CardHeader>

          <CardContent className="space-y-4">
            <div className="grid gap-4 sm:grid-cols-2">
              <div className="space-y-2">
                <Label>Site Name</Label>
                <Input
                  defaultValue="Shakuniya Solutions"
                  className="bg-slate-900 border-white/10 text-gray-200"
                />
              </div>

              <div className="space-y-2">
                <Label>Contact Email</Label>
                <Input
                  type="email"
                  defaultValue="info@shakuniya.com"
                  className="bg-slate-900 border-white/10 text-gray-200"
                />
              </div>
            </div>

            <div className="space-y-2">
              <Label>Site Description</Label>
              <Textarea
                defaultValue="Premier digital marketing and web development agency"
                className="bg-slate-900 border-white/10 text-gray-200 min-h-[100px]"
              />
            </div>
          </CardContent>
        </Card> */}

        {/* Notifications */}
        {/* <Card className="bg-slate-950 border border-white/10">
          <CardHeader>
            <CardTitle className="text-lg">
              Notifications
            </CardTitle>
          </CardHeader>

          <CardContent className="space-y-4">
            <div className="flex items-center justify-between">
              <div>
                <p className="font-medium">
                  Email Notifications
                </p>
                <p className="text-sm text-gray-400">
                  Receive email alerts for new enquiries
                </p>
              </div>
              <Switch defaultChecked />
            </div>

            <Separator className="bg-white/10" />

            <div className="flex items-center justify-between">
              <div>
                <p className="font-medium">
                  Weekly Reports
                </p>
                <p className="text-sm text-gray-400">
                  Get weekly summary of site activity
                </p>
              </div>
              <Switch />
            </div>
          </CardContent>
        </Card> */}

        {/* Save */}
        {/* <div className="flex justify-end">
          <Button
            onClick={handleSave}
            disabled={isLoading}
            className="bg-gradient-to-r from-purple-500 to-cyan-500 text-white"
          >
            {isLoading ? "Saving..." : "Save Changes"}
          </Button>
        </div>

      </div>
    </div>
  );
};

export default Settings; */}
