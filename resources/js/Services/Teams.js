import api from "./Api";

export const getBalancedTeams = async () => {
    const payload = {
        data: null,
        error: null,
    };

    try {
        const response = await api.post("/teams/generate", {});
        payload.data = response.data;
    } catch (error) {
        payload.error = error;
    }

    return payload;
};
