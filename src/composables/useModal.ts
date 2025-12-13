import { ref, h, render } from "vue";
import ConfirmModal from "@/components/common/ConfirmModal.vue";

export interface ModalOptions {
  title: string;
  message: string;
  type?: "confirm" | "alert" | "warning" | "error" | "success";
  confirmText?: string;
  cancelText?: string;
  showCancel?: boolean;
}

export function useModal() {
  /**
   * Show a confirmation dialog
   * Returns a promise that resolves to true if confirmed, false if cancelled
   */
  const confirm = (options: ModalOptions | string): Promise<boolean> => {
    return new Promise((resolve) => {
      const modalOptions: ModalOptions =
        typeof options === "string"
          ? { title: "Onay", message: options, type: "confirm" }
          : options;

      const container = document.createElement("div");
      document.body.appendChild(container);

      const isOpen = ref(true);

      const handleConfirm = () => {
        isOpen.value = false;
        cleanup();
        resolve(true);
      };

      const handleCancel = () => {
        isOpen.value = false;
        cleanup();
        resolve(false);
      };

      const cleanup = () => {
        setTimeout(() => {
          render(null, container);
          document.body.removeChild(container);
        }, 300);
      };

      const vnode = h(ConfirmModal, {
        isOpen: isOpen.value,
        title: modalOptions.title,
        message: modalOptions.message,
        type: modalOptions.type || "confirm",
        confirmText: modalOptions.confirmText,
        cancelText: modalOptions.cancelText,
        showCancel: modalOptions.showCancel ?? true,
        onConfirm: handleConfirm,
        onCancel: handleCancel,
        "onUpdate:isOpen": (value: boolean) => {
          isOpen.value = value;
          if (!value) {
            handleCancel();
          }
        },
      });

      render(vnode, container);
    });
  };

  /**
   * Show an alert dialog
   * Returns a promise that resolves when the user clicks OK
   */
  const alert = (options: ModalOptions | string): Promise<void> => {
    return new Promise((resolve) => {
      const modalOptions: ModalOptions =
        typeof options === "string"
          ? { title: "Bilgi", message: options, type: "alert" }
          : { ...options, type: options.type || "alert" };

      const container = document.createElement("div");
      document.body.appendChild(container);

      const isOpen = ref(true);

      const handleConfirm = () => {
        isOpen.value = false;
        cleanup();
        resolve();
      };

      const cleanup = () => {
        setTimeout(() => {
          render(null, container);
          document.body.removeChild(container);
        }, 300);
      };

      const vnode = h(ConfirmModal, {
        isOpen: isOpen.value,
        title: modalOptions.title,
        message: modalOptions.message,
        type: modalOptions.type || "alert",
        confirmText: modalOptions.confirmText || "Tamam",
        showCancel: false,
        onConfirm: handleConfirm,
        "onUpdate:isOpen": (value: boolean) => {
          isOpen.value = value;
          if (!value) {
            handleConfirm();
          }
        },
      });

      render(vnode, container);
    });
  };

  /**
   * Show a success message
   */
  const success = (message: string, title = "Başarılı"): Promise<void> => {
    return alert({ title, message, type: "success" });
  };

  /**
   * Show an error message
   */
  const error = (message: string, title = "Hata"): Promise<void> => {
    return alert({ title, message, type: "error" });
  };

  /**
   * Show a warning message
   */
  const warning = (message: string, title = "Uyarı"): Promise<void> => {
    return alert({ title, message, type: "warning" });
  };

  return {
    confirm,
    alert,
    success,
    error,
    warning,
  };
}
