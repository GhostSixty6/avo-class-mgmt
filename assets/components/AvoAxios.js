import axios from "axios";

// Create axios instance and set base URL
const AvoAxios = axios.create({
  baseURL: "/api/",
});

// Set our authentication token (if set) for each call
AvoAxios.interceptors.request.use(function (config) {
  const token = sessionStorage.getItem("userToken");

  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }

  return config;
});

// React to a failed api call
AvoAxios.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response.status === 401 || error.response.status === 403) {
      // Access denied so most likely, our JWT token expired or we tried to access an unauthorized page
      // Log out and send back to log in page
      sessionStorage.clear();
      window.location.href = "/login";
    } else if (error.response.status === 404) {
      // Redirect to our custom 404 page
      window.location.href = "/404";
    } else if (error.response.status !== 200) {
      // Otherwise handle all other errors with the custom error page
      window.location.href = "/error";
    }
  }
);

export default AvoAxios;
