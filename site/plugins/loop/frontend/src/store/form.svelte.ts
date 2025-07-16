import type { FormData } from '../types';

export const formData: FormData = $state({
  text: "",
  parentId: null
});

export const reset = () => {
  formData.text = ""
  formData.parentId = null
}
