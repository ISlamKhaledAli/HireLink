import api from "./api";

export const updateProfileApi = (formData) => {
  return api.post("/user/profile?_method=PATCH", formData, {
    headers: {
      "Content-Type": "multipart/form-data",
    },
  });
};
