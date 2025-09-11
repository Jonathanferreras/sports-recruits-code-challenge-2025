<script setup>
import { Head, router } from "@inertiajs/vue3";
import { ref, onMounted } from "vue";
import { getPlayers } from "@/Services/Players";
import { getBalancedTeams } from "@/Services/Teams";
import RankIcon from "@/Components/RankIcon.vue";

const players = ref([]);

const loading = ref(true);
const listError = ref("");

const generating = ref(false);
const actionError = ref("");

async function loadPlayers() {
    loading.value = true;
    listError.value = "";
    const { data, error } = await getPlayers();
    if (error) {
        listError.value = error;
        players.value = [];
    } else {
        players.value = data || [];
    }
    loading.value = false;
}

async function handleGenerateBalanceTeam() {
    actionError.value = "";
    generating.value = true;

    const { data, error } = await getBalancedTeams();

    generating.value = false;

    if (error) {
        actionError.value = error;
        return;
    }
    if (data?.id) {
        router.visit(`/results?id=${encodeURIComponent(data.id)}`);
    } else {
        actionError.value = "No result id returned.";
    }
}

onMounted(loadPlayers);
</script>

<template>
    <Head title="App" />

    <div class="p-4">
        <h1 class="text-xl font-semibold mb-2">
            Sports Recruits Code Challenge
        </h1>
        <div
            v-if="listError"
            class="mb-3 rounded-md border border-red-200 bg-red-50 text-red-700 px-4 py-2 text-sm flex items-start justify-between gap-4"
            role="alert"
            aria-live="assertive"
        >
            <div>{{ listError }}</div>
            <button
                class="shrink-0 rounded bg-red-600 px-3 py-1 text-white hover:bg-red-500"
                @click="loadPlayers"
            >
                Retry
            </button>
        </div>
        <div
            class="sticky top-0 z-20 bg-white/95 backdrop-blur supports-[backdrop-filter]:bg-white/60 shadow-sm"
        >
            <div class="px-4 py-3 border-b flex items-center gap-3">
                <button
                    :disabled="generating || !!listError || loading"
                    class="bg-green-500 hover:bg-green-400 disabled:opacity-60 text-white font-bold py-2 px-4 border-b-4 border-green-700 hover:border-green-500 rounded"
                    @click="handleGenerateBalanceTeam"
                >
                    {{ generating ? "Generating…" : "Generate Balanced Teams" }}
                </button>
                <span v-if="actionError" class="ml-auto text-sm text-red-600">
                    {{ actionError }}
                </span>
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
            <div
                v-if="!players.length && !listError"
                class="px-3 py-6 text-sm text-gray-500"
            >
                No players found.
            </div>
        </div>
    </div>
</template>
