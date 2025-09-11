<script setup>
import { Head } from "@inertiajs/vue3";
import { ref, onMounted } from "vue";
import { getPlayers } from "../Services/Players";
import RankIcon from "../Components/RankIcon.vue";

const players = ref([]);

onMounted(async () => {
    const { data, error } = await getPlayers();

    if (error) {
        // display error message
    } else if (data && !error) {
        players.value = data;
    }
});

const handleGenerateBalanceTeam = () => {
    // implement handler
};
</script>

<template>
    <Head title="App" />

    <div class="p-4">
        <h1 class="text-xl font-semibold mb-4">
            Sports Recruits Code Challenge
        </h1>
        <div
            class="sticky top-0 z-20 bg-white/95 backdrop-blur supports-[backdrop-filter]:bg-white/60 shadow-sm"
        >
            <div class="px-4 py-3 border-b">
                <button
                    class="bg-green-500 hover:bg-green-400 text-white font-bold py-2 px-4 border-b-4 border-green-700 hover:border-green-500 rounded"
                    @click="handleGenerateBalanceTeam"
                >
                    Generate Balanced Teams
                </button>
            </div>

            <div
                class="hidden md:grid md:grid-cols-4 md:gap-4 md:px-3 md:py-2 md:text-xs md:font-semibold md:uppercase md:tracking-wide md:text-gray-600 border-b"
            >
                <div>#</div>
                <div>Name</div>
                <div>Rank</div>
                <div>Can Play Goalie</div>
            </div>
        </div>

        <div class="divide-y divide-gray-200 md:divide-y-0 md:mt-2">
            <div
                v-for="(p, i) in players"
                :key="p.id"
                class="py-3 md:grid md:grid-cols-4 md:gap-4 md:items-center md:px-3 md:rounded-lg md:hover:bg-gray-50"
            >
                <div class="px-3 md:px-0">
                    <div class="md:hidden text-xs font-semibold text-gray-500">
                        #
                    </div>
                    <div
                        class="mt-1 md:mt-0 flex items-center gap-2 tabular-nums"
                    >
                        {{ i + 1 }}
                    </div>
                </div>

                <div class="px-3 md:px-0">
                    <div class="md:hidden text-xs font-semibold text-gray-500">
                        Name
                    </div>
                    <div class="mt-1 md:mt-0 font-medium">
                        {{ p.first_name }} {{ p.last_name }}
                    </div>
                </div>

                <div class="px-3 md:px-0">
                    <div class="md:hidden text-xs font-semibold text-gray-500">
                        Rank
                    </div>
                    <div class="mt-1 md:mt-0 flex items-center gap-2">
                        <RankIcon :value="p.ranking" :size="30" />
                    </div>
                </div>

                <div class="px-3 md:px-0">
                    <div class="md:hidden text-xs font-semibold text-gray-500">
                        Can Play Goalie
                    </div>
                    <div
                        class="mt-1 md:mt-0 text-sm"
                        :class="
                            p.can_play_goalie
                                ? 'text-emerald-600 font-bold'
                                : 'text-gray-400'
                        "
                    >
                        {{ p.can_play_goalie ? "Yes" : "No" }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
