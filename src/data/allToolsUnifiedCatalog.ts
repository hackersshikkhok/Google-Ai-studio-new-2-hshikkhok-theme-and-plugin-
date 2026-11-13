import { DeveloperToolSpec, DEVELOPER_TOOLS_CATALOG } from './platformData';
import { EXTRA_500_PRO_TOOLS } from './extra500ToolsCatalog';
import { MASTER_1000_TOOLS_CATALOG } from './master1000ToolsCatalog';
import { NEXTGEN_498_PRO_TOOLS } from './nextgen498ToolsCatalog';

const rawList: DeveloperToolSpec[] = [
  ...DEVELOPER_TOOLS_CATALOG,
  ...EXTRA_500_PRO_TOOLS,
  ...MASTER_1000_TOOLS_CATALOG,
  ...NEXTGEN_498_PRO_TOOLS
];

const seen = new Set<string>();
export const ALL_UNIFIED_TOOLS_CATALOG: DeveloperToolSpec[] = rawList.filter((tool) => {
  if (seen.has(tool.slug)) {
    return false;
  }
  seen.add(tool.slug);
  return true;
});
