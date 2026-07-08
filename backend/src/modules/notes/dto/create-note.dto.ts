import { IsString, IsBoolean, IsOptional, IsUUID } from 'class-validator';

export class CreateNoteDto {
  @IsUUID()
  tripId: string;

  @IsString()
  content: string;

  @IsOptional()
  @IsBoolean()
  isPinned?: boolean;
}

export class UpdateNoteDto {
  @IsOptional()
  @IsString()
  content?: string;

  @IsOptional()
  @IsBoolean()
  isPinned?: boolean;
}
