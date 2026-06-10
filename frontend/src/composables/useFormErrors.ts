import { ref } from 'vue';

export function useFormErrors() {
  const errors = ref<Record<string, string>>({});
  const generalError = ref('');

  function setErrors(fieldErrors: Record<string, string[]>): void {
    errors.value = {};
    generalError.value = '';

    for (const [field, messages] of Object.entries(fieldErrors)) {
      if (messages[0]) {
        errors.value[field] = messages[0];
      }
    }
  }

  function clearErrors(): void {
    errors.value = {};
    generalError.value = '';
  }

  function fieldError(field: string): string | undefined {
    return errors.value[field];
  }

  return {
    errors,
    generalError,
    setErrors,
    clearErrors,
    fieldError,
  };
}
