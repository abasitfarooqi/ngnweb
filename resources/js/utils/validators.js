// File: resources/js/utils/validators.js
export function isValidEmail(email) {
  const re = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(\".+\"))@(([^<>()[\]\\.,;:\s@"]+\.)+[^<>()[\]\\.,;:\s@"]{2,})$/i;
  return re.test(String(email).toLowerCase());
}

export function isPositiveNumber(value) {
  return typeof value === 'number' && value > 0;
}

// Validate Product
export function isValidProduct(x) {
  // Implement product validation logic
  return (
    typeof x.id === 'number' &&
    typeof x.name === 'string' &&
    typeof x.image_url === 'string' &&
    typeof x.normal_price === 'number' &&
    (typeof x.discount_price === 'number' || x.discount_price === null) &&
    (typeof x.discount_percentage === 'number' || x.discount_percentage === null) &&
    (typeof x.points === 'number' || x.points === null) &&
    typeof x.brand === 'string' &&
    typeof x.in_stock === 'boolean' &&
    typeof x.rating === 'number' &&
    typeof x.review_count === 'number' &&
    (typeof x.global_stock === 'number' || x.global_stock === null)
  );
}

// Validate Brand
export function isValidBrand(x) {
  return (
    typeof x.id === 'number' &&
    typeof x.name === 'string' &&
    (typeof x.image_url === 'string' || x.image_url === null) &&
    (typeof x.slug === 'string' || x.slug === '') &&
    (typeof x.description === 'string' || x.description === '')
  );
}

// Validate Category
export function isValidCategory(x) {
  return (
    typeof x.id === 'number' &&
    typeof x.name === 'string' &&
    (typeof x.image_url === 'string' || x.image_url === null) &&
    (typeof x.slug === 'string' || x.slug === '') &&
    (typeof x.description === 'string' || x.description === '')
  );
}

