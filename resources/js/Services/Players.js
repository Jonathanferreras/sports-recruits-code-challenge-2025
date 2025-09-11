import api from "./Api";

export const getPlayers = async () => {
    const payload = {
        data: null,
        error: null,
    };

    try {
        const response = await api.get("/players");
        payload.data = response.data;
    } catch (error) {
        payload.error = error;
    }

    return payload;
};
