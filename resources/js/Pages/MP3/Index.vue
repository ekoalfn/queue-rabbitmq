<template>
  <Head title="Play MP3" />

  <AuthenticatedLayout>
      <template #header>
          <h2 class="font-semibold text-xl text-gray-800 leading-tight">MP3</h2>
      </template>

      <div class="container mx-auto p-4">
          <form @submit.prevent="handleSubmit" class="space-y-4">
              <div>
                  <label for="number" class="block text-sm font-medium text-gray-700">Nomor Antrian:</label>
                  <input type="number" id="number" v-model="number" min="1" max="100" required class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
              </div>
              <div>
                  <label for="loket" class="block text-sm font-medium text-gray-700">Loket:</label>
                  <select id="loket" v-model="loket" required class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                      <option value="" disabled>Pilih Loket</option>
                      <option v-for="loket in lokets" :key="loket" :value="loket">{{ loket }}</option>
                  </select>
              </div>
              <button type="submit" class="w-full inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">Play MP3</button>
          </form>
          <audio ref="audio" @ended="onAudioEnded" class="mt-4"></audio>
      </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, onMounted, nextTick } from 'vue';
import { Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import axios from 'axios';

const number = ref('');
const loket = ref('');
const lokets = ['A', 'B', 'C', 'D', 'E'];
const audioParts = ref([]);
const currentPartIndex = ref(0);
const audioRef = ref(null);

onMounted(() => {
audioRef.value = document.querySelector('audio');
});

const handleSubmit = () => {
    axios.post('/play-mp3', {
        number: number.value,
        loket: loket.value,
    })
    .then(response => {
        console.log('Response from backend:', response.data);
    })
    .catch(error => {
        console.error('Error sending request:', error);
    });
};



const prepareAudioParts = (number, loket) => {
console.log('Preparing audio parts');
audioParts.value = [
  '/audio/antrian_nomor.mp3',
  `/audio/nomor_${number}.mp3`,
  '/audio/silahkan_menuju_loket.mp3',
  `/audio/loket_${loket}.mp3`
];
currentPartIndex.value = 0;
console.log('Audio parts:', audioParts.value);
};

const playAudio = () => {
if (currentPartIndex.value < audioParts.value.length && audioRef.value) {
  audioRef.value.src = audioParts.value[currentPartIndex.value];
  audioRef.value.play().catch(error => {
    console.error('Error playing audio:', error);
  });
} else {
  console.log('No more audio parts to play or audioRef is null');
}
};

const onAudioEnded = () => {
currentPartIndex.value++;
playAudio();
};
</script>
