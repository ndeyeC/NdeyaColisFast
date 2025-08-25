import { CapacitorConfig } from '@capacitor/cli';

const config: CapacitorConfig = {
  appId: 'com.colisfast.app',
  appName: 'ColisFast',
  webDir: 'public/mobile',
  server: {
    // Retiré androidScheme pour permettre HTTP en développement
    url: 'http://10.0.2.2:8000', // Android emulator
    cleartext: true,
    allowNavigation: ["*"], // Permet toute navigation
    hostname: '10.0.2.2'
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