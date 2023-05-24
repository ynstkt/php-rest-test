export function useOrderBy() {
  const orderBy = <T, K extends keyof T>(items: T[], key: K, desc: boolean = false) => {
    const tmpItems = [...items];
    if(items && items.length > 0) {
      tmpItems.sort((a: T, b: T) => {
        if (a[key] < b[key]) {
            return -1 * (desc ? -1 : 1);
        } else if (a[key] > b[key]) {
            return 1 * (desc ? -1 : 1);
        } else {
            return 0;
        }
      });
    }
    return tmpItems;
  };

  return {
    orderBy,
  };
}

