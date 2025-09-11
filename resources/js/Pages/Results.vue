<script setup>
import { ref } from "vue";
import { Head, Link, router } from "@inertiajs/vue3";
import RankIcon from "@/Components/RankIcon.vue";
import { getBalancedTeams } from "@/Services/Teams";

const props = defineProps({
    id: { type: String, default: "" },
    teams: { type: Array, default: () => [] },
});

const generating = ref(false);
const errorMsg = ref("");

async function handleReGenerate() {
    errorMsg.value = "";
    generating.value = true;
    const { data, error } = await getBalancedTeams();
    generating.value = false;

    if (error) {
        errorMsg.value = error;
        return;
    }
    if (data?.id) {
        router.visit(`/results?id=${encodeURIComponent(data.id)}`);
    } else {
        errorMsg.value = "No result id returned.";
    }
}
</script>

<template>
    <Head title="Results" />

    <div class="p-4">
        <h1 class="text-xl font-semibold mb-2">
            <Link href="/" class="text-sky-600 hover:underline text-sm"
                >← Back</Link
            >
        </h1>
        <p class="text-xs text-gray-500 mb-4">Result ID: {{ props.id }}</p>
        <div
            class="sticky top-0 z-20 bg-white/95 backdrop-blur supports-[backdrop-filter]:bg-white/60 shadow-sm"
        >
            <div class="px-4 py-3 border-b flex items-center gap-3">
                <button
                    :disabled="generating"
                    class="bg-green-500 hover:bg-green-400 disabled:opacity-60 text-white font-bold py-2 px-4 border-b-4 border-green-700 hover:border-green-500 rounded"
                    @click="handleReGenerate"
                >
                    {{ generating ? "Generating…" : "Re-Generate Teams" }}
                </button>

                <span v-if="errorMsg" class="text-sm text-red-600 ml-auto">
                    {{ errorMsg }}
                </span>
            </div>
            <div
                class="hidden md:grid md:grid-cols-2 gap-4 px-4 py-2 border-b md:text-xs md:font-semibold md:uppercase md:tracking-wide md:text-gray-600"
            >
                <div
                    class="grid grid-cols-[5rem_1fr_8rem_12rem] items-center gap-4"
                >
                    <div>#</div>
                    <div>Name</div>
                    <div>Rank</div>
                    <div>Can Play Goalie</div>
                </div>
                <div
                    class="grid grid-cols-[5rem_1fr_8rem_12rem] items-center gap-4"
                >
                    <div>#</div>
                    <div>Name</div>
                    <div>Rank</div>
                    <div>Can Play Goalie</div>
                </div>
            </div>
        </div>
        <div class="grid gap-6 md:grid-cols-2 md:mt-2">
            <div
                v-for="team in props.teams"
                :key="team.name"
                class="rounded-lg border border-gray-200 overflow-hidden"
            >
                <div
                    class="flex items-center justify-between px-4 py-2 border-b bg-gray-50"
                >
                    <h2 class="text-sm font-semibold">{{ team.name }}</h2>
                    <div class="text-xs text-gray-500">
                        <span class="mr-3"
                            >{{ team.players?.length || 0 }} players</span
                        >
                        <span v-if="team.total_ranking !== undefined">
                            total rank:
                            <span class="tabular-nums">{{
                                team.total_ranking
                            }}</span>
                        </span>
                    </div>
                </div>
                <div class="divide-y divide-gray-200">
                    <div
                        v-for="(p, i) in team.players || []"
                        :key="p.id ?? i"
                        class="px-4 py-3 grid md:grid-cols-[5rem_1fr_8rem_12rem] items-center gap-4 md:hover:bg-gray-50"
                    >
                        <div>
                            <div
                                class="md:hidden text-xs font-semibold text-gray-500"
                            >
                                #
                            </div>
                            <div class="mt-1 md:mt-0 tabular-nums">
                                {{ i + 1 }}
                            </div>
                        </div>

                        <div>
                            <div
                                class="md:hidden text-xs font-semibold text-gray-500"
                            >
                                Name
                            </div>
                            <div class="mt-1 md:mt-0 font-medium">
                                {{ p.first_name }} {{ p.last_name }}
                            </div>
                        </div>

                        <div>
                            <div
                                class="md:hidden text-xs font-semibold text-gray-500"
                            >
                                Rank
                            </div>
                            <div class="mt-1 md:mt-0 flex items-center gap-2">
                                <RankIcon :value="p.ranking" :size="26" />
                            </div>
                        </div>

                        <div>
                            <div
                                class="md:hidden text-xs font-semibold text-gray-500"
                            >
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
                        v-if="!team.players || team.players.length === 0"
                        class="px-4 py-6 text-sm text-gray-500"
                    >
                        No players.
                    </div>
                </div>
            </div>
        </div>
        <div v-if="!props.teams.length" class="text-gray-500 text-sm mt-6">
            No results yet. Use “Re-Generate Teams”.
        </div>
    </div>
</template>
