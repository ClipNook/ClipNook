/* @vite-ignore */
import {
    Livewire,
    Alpine,
} from "../../vendor/livewire/livewire/dist/livewire.esm";

// Make Alpine and Livewire available as global variables
window.Alpine = Alpine;
window.Livewire = Livewire;

// Initialize Livewire
Livewire.start();
