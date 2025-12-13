import { ref, computed, onMounted, onUnmounted } from "vue";
import { useToast } from "vue-toastification";

export interface VoiceCommand {
  command: string;
  action: () => void;
  aliases?: string[];
}

export function useVoiceAssistant() {
  const toast = useToast();
  const isListening = ref(false);
  const isSupported = ref(false);
  const transcript = ref("");
  const interimTranscript = ref("");
  const commands = ref<VoiceCommand[]>([]);
  
  let recognition: any = null;

  // Check browser support
  const checkSupport = () => {
    const SpeechRecognition =
      (window as any).SpeechRecognition ||
      (window as any).webkitSpeechRecognition;
    
    isSupported.value = !!SpeechRecognition;
    return isSupported.value;
  };

  // Initialize speech recognition
  const initRecognition = () => {
    if (!checkSupport()) {
      toast.warning("Tarayıcınız ses tanıma özelliğini desteklemiyor");
      return false;
    }

    const SpeechRecognition =
      (window as any).SpeechRecognition ||
      (window as any).webkitSpeechRecognition;

    recognition = new SpeechRecognition();
    recognition.continuous = true;
    recognition.interimResults = true;
    recognition.lang = "tr-TR";

    recognition.onstart = () => {
      isListening.value = true;
      toast.success("Ses asistanı başlatıldı");
    };

    recognition.onend = () => {
      const wasListening = isListening.value;
      isListening.value = false;
      if (wasListening) {
        // Restart if it was supposed to be listening
        try {
          recognition.start();
        } catch (error) {
          console.error("Failed to restart recognition:", error);
        }
      }
    };

    recognition.onerror = (event: any) => {
      console.error("Speech recognition error:", event.error);
      isListening.value = false;

      if (event.error === "not-allowed") {
        toast.error("Mikrofon izni gereklidir");
      } else if (event.error === "no-speech") {
        toast.warning("Ses algılanamadı");
      } else {
        toast.error("Ses tanıma hatası: " + event.error);
      }
    };

    recognition.onresult = (event: any) => {
      let interimText = "";
      let finalText = "";

      for (let i = event.resultIndex; i < event.results.length; i++) {
        const result = event.results[i];
        if (result.isFinal) {
          finalText += result[0].transcript;
        } else {
          interimText += result[0].transcript;
        }
      }

      interimTranscript.value = interimText;
      
      if (finalText) {
        transcript.value = finalText;
        processCommand(finalText.toLowerCase().trim());
      }
    };

    return true;
  };

  // Start listening
  const start = () => {
    if (!recognition) {
      if (!initRecognition()) {
        return;
      }
    }

    if (!isListening.value) {
      try {
        recognition.start();
      } catch (error) {
        console.error("Failed to start recognition:", error);
        toast.error("Ses tanıma başlatılamadı");
      }
    }
  };

  // Stop listening
  const stop = () => {
    if (recognition && isListening.value) {
      isListening.value = false;
      recognition.stop();
      toast.info("Ses asistanı durduruldu");
    }
  };

  // Toggle listening
  const toggle = () => {
    if (isListening.value) {
      stop();
    } else {
      start();
    }
  };

  // Register a voice command
  const registerCommand = (command: VoiceCommand) => {
    commands.value.push(command);
  };

  // Register multiple commands
  const registerCommands = (newCommands: VoiceCommand[]) => {
    commands.value.push(...newCommands);
  };

  // Process recognized speech
  const processCommand = (text: string) => {
    console.log("Processing command:", text);

    // Find matching command
    const matchedCommand = commands.value.find((cmd) => {
      const commandText = cmd.command.toLowerCase();
      const aliases = cmd.aliases?.map((a) => a.toLowerCase()) || [];
      
      return (
        text.includes(commandText) ||
        aliases.some((alias) => text.includes(alias))
      );
    });

    if (matchedCommand) {
      speak(`${matchedCommand.command} komutu çalıştırılıyor`);
      matchedCommand.action();
    } else {
      speak("Üzgünüm, bu komutu anlamadım");
    }
  };

  // Text-to-speech
  const speak = (text: string) => {
    if ("speechSynthesis" in window) {
      const utterance = new SpeechSynthesisUtterance(text);
      utterance.lang = "tr-TR";
      utterance.rate = 1.0;
      utterance.pitch = 1.0;
      window.speechSynthesis.speak(utterance);
    }
  };

  // Set language
  const setLanguage = (lang: string) => {
    if (recognition) {
      recognition.lang = lang;
    }
  };

  // Cleanup
  onUnmounted(() => {
    stop();
  });

  return {
    isListening,
    isSupported,
    transcript,
    interimTranscript,
    commands: computed(() => commands.value),
    start,
    stop,
    toggle,
    registerCommand,
    registerCommands,
    speak,
    setLanguage,
    checkSupport,
  };
}
