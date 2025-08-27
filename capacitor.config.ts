import { CapacitorConfig } from '@capacitor/cli';

const config: CapacitorConfig = {
  appId: 'com.colisfast.app',
  appName: 'ColisFast',
  webDir: 'public/mobile',
  server: {
    url: 'http://192.168.1.24:8000',
    cleartext: true,
    allowNavigation: ["*"],
  },
  plugins: {
    SplashScreen: {
      launchShowDuration: 2000,
      backgroundColor: '#667eea',
      showSpinner: true,
      spinnerColor: '#ffffff'
    },
    StatusBar: {
      backgroundColor: '#667eea',
      style: 'Light'
    },
    Keyboard: {
      resize: 'body',
      style: 'dark'
    },
    PushNotifications: {
      presentationOptions: ['badge', 'sound', 'alert']
    },
    Geolocation: {
      permissions: ['location']
    }
  }
};

export default config;