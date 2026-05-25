document.addEventListener("DOMContentLoaded", function () {
    const priceElements = document.querySelectorAll(".model-price[data-price-eur]");

    if (priceElements.length === 0) {
        return;
    }

    fetch("ajax/get_currency.php")
        .then(function (response) {
            if (!response.ok) {
                throw new Error("Currency request failed");
            }

            return response.json();
        })
        .then(function (data) {
            if (!data.success || !data.rate) {
                throw new Error("Invalid currency data");
            }

            const rate = Number(data.rate);

            priceElements.forEach(function (priceElement) {
                const eurPrice = Number(priceElement.dataset.priceEur);
                const usdElement = priceElement.parentElement.querySelector(".usd-price");

                if (!usdElement || !Number.isFinite(eurPrice)) {
                    return;
                }

                const usdPrice = eurPrice * rate;

                usdElement.textContent = "≈ $" + usdPrice.toLocaleString("en-US", {
                    maximumFractionDigits: 0
                });
            });
        })
        .catch(function (error) {
            console.error("Currency conversion error:", error);

            document.querySelectorAll(".usd-price").forEach(function (usdElement) {
                usdElement.textContent = "USD unavailable";
            });
        });
});