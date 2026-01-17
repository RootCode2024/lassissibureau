import "./bootstrap";
import { createIcons, icons } from "lucide";

// Initialiser les icônes au chargement
document.addEventListener("DOMContentLoaded", () => {
    createIcons({ icons });
});

// Réinitialiser après chaque mise à jour Livewire
document.addEventListener("livewire:init", () => {
    Livewire.hook("morph.updated", () => {
        createIcons({ icons });
    });
});

// Réinitialiser après la navigation Livewire (SPA mode)
document.addEventListener("livewire:navigated", () => {
    createIcons({ icons });
});

// Export global pour utilisation dans les scripts inline
window.refreshLucideIcons = () => {
    createIcons({ icons });
};
